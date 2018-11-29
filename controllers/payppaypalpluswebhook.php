<?php
/**
 * This file is part of OXID eSales PayPal Plus module.
 *
 * OXID eSales PayPal Plus module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal Plus module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal Plus module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class paypPayPalPlusWebhook
 *
 * This provides the endpoint for request from PayPal
 */
class paypPayPalPlusWebhook extends oxUBase
{

    /** @var string */
    protected $_sThisTemplate = 'page/webhook/response.tpl';

    /** HTTP 202 status code header*/
    const HTTP_HEADER_202 = 'HTTP/1.1 202 Accepted';

    /** HTTP 400 status code header */
    const HTTP_HEADER_503 = 'HTTP/1.1 503 Service Unavailable';

    /**
     * Config wrapper instance
     *
     * @var null|paypPayPalPlusSuperCfg
     */
    protected $_oPayPalPlusSuperCfg = null;

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * @inheritdoc
     *
     * Receives the request to webhook from PayPal
     * Validates webhook to ensure it is originating from PayPal
     * Optionally logs the request
     * Sends response
     */
    public function render()
    {
        /** @var string $sHeaderCode Send 202 Header if everything went fine and PayPal will not send the event again */
        $sHeaderCode = paypPayPalPlusWebhook::HTTP_HEADER_202;
        /** @var string $sStatus is for humans only */
        $sStatus = 'OK';

        $mReturn = $this->_paypPayPalPlusWebhook_render_parent();

        /** @var string $sRequestBody Get the payload of the REQUEST, this is a string in JSON format */
        $sRequestBody = $this->_getRequestBody();

        try {
            /**
             * By default any webhook event is validated.
             * For testing set the eventhandler to simulation mode by setting IDebug > 0 in config.inc.php
             * and adding the parameter 'simulate' to the webhook URL
             */
            $blValidateRequest = $this->isSimulationMode() ? false : true;
            /**  @var \PayPal\Api\WebhookEvent $oWebhookEvent */
            $oWebhookEvent = $this->_getWebhookEvent($sRequestBody, $blValidateRequest);

            /** Process the event */
            $this->_processEvent($oWebhookEvent);
        } catch (Exception $oException) {
            /**
             * If anything went wrong sent a HTTP 400 status, so PayPal will resend the webhook
             */
            $sHeaderCode = paypPayPalPlusWebhook::HTTP_HEADER_503;
            /** @var string $sStatus There may be different sources for the message */
            $sStatus = method_exists($oException, 'getData') && $oException->getData() ? $oException->getData() : $oException->getMessage();

            /** Prints the exception to the screen, if debug mode is enabled in the module */
            $oPayPalPlusSuperCfg = $this->getPayPalPlusSuperCfg();
            $oPayPalPlusSuperCfg->getShop()->getErrorHandler()->debug($oException);
        }

        /** The response code is what PayPal processes, */
        $this->_sendResponseHeader($sHeaderCode);
        /** the view is only for humans */
        $this->_setViewData(array('status' => $sStatus));

        return $mReturn;
    }

    /**
     * Process the received webhook.
     * Retrieves the corresponding payment stored in the shop and updates the payment status.
     * If the status is "completed", the corresponding order is set to paid.
     * The orders' paid date is the resource update_time of the event
     *
     * @param $oWebhookEvent
     *
     * @throws paypPayPalPlusNoPaymentFoundException
     * @throws paypPayPalPlusPaymentDataSaveException
     */
    protected function _processEvent(\PayPal\Api\WebhookEvent $oWebhookEvent)
    {
        /**
         * Retrieve the PayPal Payment Id and the event type from the event
         */
        $sPaymentId = $this->getShop()->getDataAccess()->invokeGet($oWebhookEvent->getResource(), 'parent_payment:$');
        $sEventType = $oWebhookEvent->getEventType();

        /**
         * Retrieve the corresponding Payment as stored in the shop database
         */
        $oPaymentData = $this->_getPaymentDataModel();
        $oPaymentData->loadByPaymentId($sPaymentId);
        if (!$oPaymentData->isLoaded()) {
            $this->_throwNoPaymentFoundException();
        };

        /**
         * Update the payment status in the shop database
         * and set the corresponding order to paid if the state is completed
         */
        $oPaymentData->setStatusByEventType($sEventType);
        if (!$oPaymentData->save()) {
            $this->_throwPaymentDataSaveException();
        }
        if ($oPaymentData->getStatus() == $this->getShop()->getPayPalPlusConfig()->getRefundablePaymentStatus()) {
            $sDatetime = $this->getShop()->getDataAccess()->invokeGet($oWebhookEvent->getResource(), 'update_time:$');
            $oPaymentData->setOrderPaid($sDatetime);
        }
    }

    /**
     * Get the body of the PayPal request
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    protected function _getRequestBody()
    {
        return file_get_contents('php://input');
    }

    /**
     * Convert the body data of the REQUEST into an instance of a WebhookEvent.
     * By default the REQUEST will be validated by re-requesting the same Event Id again from PayPal.
     *
     * @param      $sRequestBody
     * @param bool $blValidateRequest
     *
     * @return \PayPal\Api\WebhookEvent
     * @throws \PayPal\Exception\PayPalConnectionException
     */
    protected function _getWebhookEvent($sRequestBody, $blValidateRequest = true)
    {
        $oWebhookEvent = null;

        try {
            /**
             * Get API context and SDK from the Config Instance
             */
            $oPayPalPlusSuperCfg = $this->getPayPalPlusSuperCfg();
            /** @var PayPal\Rest\ApiContext $oApiContext */
            $oApiContext = $oPayPalPlusSuperCfg->getShop()->getPayPalPlusSession()->getApiContext();
            /** @var paypPayPalPlusSdk $oSdk */
            $oSdk = $oPayPalPlusSuperCfg->getSdk();

            /**
             * Get a WebhookEvent instance from The SDK wrapper
             *
             * @var \PayPal\Api\WebhookEvent $oWebhookEvent
             */
            $oWebhookEvent = $oSdk->newWebhookEvent();

            /** Validate the the REQUEST. Validation also fills the WebhookEvent object with data */
            if ($blValidateRequest) {
                $oWebhookEvent = $oWebhookEvent::validateAndGetReceivedEvent($sRequestBody, $oApiContext);
                /**  or get the data directly from the body */
            } else {
                $oWebhookEvent = $oWebhookEvent->fromJson($sRequestBody);
            }
        } catch (\InvalidArgumentException $oException) {
            $this->_throwInvalidArgumentException($oException);
        } catch (PayPal\Exception\PayPalConnectionException  $oException) {
            $this->_throwPayPalConnectionException($oException);
        }

        return $oWebhookEvent;
    }


    /**
     * Send a HTTP header
     *
     * @param $sHeaderCode
     */
    protected function _sendResponseHeader($sHeaderCode)
    {
        $oHeader = oxNew("oxHeader");
        $oHeader->setHeader($sHeaderCode);
        $oHeader->sendHeader();
    }

    /**
     * Set aViewData key and value
     *
     * @codeCoverageIgnore
     *
     * @param array $aArray
     */
    protected function _setViewData($aArray)
    {
        $this->_aViewData = array_merge($this->_aViewData, $aArray);
    }

    /**
     * If the shop is in debug mode and the REQUEST parameter simulate added to the Webhook REQUEST,
     * then the webhook event handler switches to simulation mode
     *
     * @return bool
     */
    public function isSimulationMode()
    {
        $oPayPalPlusSuperCfg = $this->getPayPalPlusSuperCfg();
        $iDebugMode = $oPayPalPlusSuperCfg->getConfig()->getConfigParam('iDebug');
        $blSimulate = (bool) $oPayPalPlusSuperCfg->getConfig()->getRequestParameter('simulate');
        $blIsSimulationMode = $iDebugMode && $blSimulate;

        return $blIsSimulationMode;
    }

    /**
     * Getter for an instance of paypPayPalPlusSuperCfg
     *
     * @codeCoverageIgnore
     *
     * @return null|object|paypPayPalPlusSuperCfg
     *
     */
    public function getPayPalPlusSuperCfg()
    {
        if (is_null($this->_oPayPalPlusSuperCfg)) {
            $this->_oPayPalPlusSuperCfg = oxNew('paypPayPalPlusSuperCfg');
        }

        return $this->_oPayPalPlusSuperCfg;
    }

    /**
     * Get OXID eShop wrapper.
     *
     * @return paypPayPalPlusShop
     */
    public function getShop()
    {
        if (is_null($this->_oShop)) {
            $this->_oShop = paypPayPalPlusShop::getShop();
        }

        return $this->_oShop;
    }

    /**
     * Throw exception - this is for unit testing
     *
     * @throws oxException
     *
     * @codeCoverageIgnore
     */
    protected function _throwNoPaymentFoundException()
    {
        /** @var paypPayPalPlusNoPaymentFoundException $oEx */
        $oEx = $this->getShop()->getNew('paypPayPalPlusNoPaymentFoundException');
        $sMessage = $this->getShop()->translate('payp_PAYPALPLUS_ERROR_NO_PAYMENT_FOUND_FOR_EVENT');
        $oEx->setMessage($sMessage);
        throw $oEx;
    }

    /**
     * Throw exception - this is for unit testing
     *
     * @param InvalidArgumentException $oException
     *
     * @codeCoverageIgnore
     */
    protected function _throwInvalidArgumentException(\InvalidArgumentException $oException)
    {
        throw $oException;
    }

    /**
     * Throw exception - this is for unit testing
     *
     * @param \PayPal\Exception\PayPalConnectionException $oException
     *
     * @codeCoverageIgnore
     *
     * @throws \PayPal\Exception\PayPalConnectionException
     */
    protected function _throwPayPalConnectionException(PayPal\Exception\PayPalConnectionException $oException)
    {
        throw $oException;
    }

    /**
     * Throw exception - this is for unit testing
     *
     * @codeCoverageIgnore
     *
     * @throws paypPayPalPlusPaymentDataSaveException
     */
    protected function _throwPaymentDataSaveException()
    {
        /** @var paypPayPalPlusPaymentDataSaveException $oEx */
        $oEx = $this->getShop()->getNew('paypPayPalPlusPaymentDataSaveException');
        $sMessage = $this->getShop()->translate('payp_PAYPALPLUS_ERROR_PAYMENT_DATA_NOT_SAVED');
        $oEx->setMessage($sMessage);
        throw $oEx;
    }

    /**
     * Calls parent render() method - this is for unit testing
     *
     * @return mixed
     */
    protected function _paypPayPalPlusWebhook_render_parent()
    {
        return parent::render();
    }

    /**
     * Get instance of PayPalPlusPaymentData class  - this is for unit testing
     *
     * @codeCoverageIgnore
     *
     * @return paypPayPalPlusPaymentData
     */
    protected function _getPaymentDataModel()
    {
        $oPaymentData = oxNew('paypPayPalPlusPaymentData');

        return $oPaymentData;
    }
}