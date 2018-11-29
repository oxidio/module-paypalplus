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
 * @link      http://www.paypal.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

/**
 * Class Admin_paypPayPalPlusOrderTab.
 *
 * Admin Order PayPal Plus tab controller.
 *
 * Collects and previews PayPal Plus payments data and controls.
 * the actions with them.
 *
 * Admin menu: Administer Orders -> Orders -> PayPal Plus
 */
class Admin_paypPayPalPlusOrderTab extends oxAdminDetails
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'payppaypalplusorder.tpl';

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * Data access utils instance.
     *
     * @var null|paypPayPalPlusDataAccess
     */
    protected $_oDataAccess = null;

    /**
     * Data converted utils instance.
     *
     * @var null|paypPayPalPlusDataConverter
     */
    protected $_oDataConverter = null;

    /**
     * OXID eShop order model object.
     *
     * @var null|oxOrder
     */
    protected $_oOrder = null;

    /**
     * PayPal Plus Payment model object.
     *
     * @var null|paypPayPalPlusPaymentData
     */
    protected $_oOrderPayment = null;

    /**
     * Refunding process error message.
     *
     * @var string
     */
    protected $_sRefundErrorMessage = '';

    /**
     * A number of remaining, possible refunds to make for current order payment.
     *
     * @var null|int
     */
    protected $_iRemainingRefunds = null;

    /**
     * An amount still possible to refund for current order payment.
     *
     * @var null|double
     */
    protected $_dRemainingRefundAmount = null;


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
     * Get data access utils instance.
     *
     * @return paypPayPalPlusDataAccess
     */
    public function getDataUtils()
    {
        if (is_null($this->_oDataAccess)) {
            $this->_oDataAccess = $this->getShop()->getDataAccess();
        }

        return $this->_oDataAccess;
    }

    /**
     * Get data converter utils instance.
     *
     * @return paypPayPalPlusDataConverter
     */
    public function getDataConverter()
    {
        if (is_null($this->_oDataConverter)) {
            $this->_oDataConverter = $this->getShop()->getConverter();
        }

        return $this->_oDataConverter;
    }

    /**
     * Get OXID eShop order object if it's loaded.
     *
     * @return null|oxOrder
     */
    public function getOrder()
    {
        if (is_null($this->_oOrder)) {

            /** @var oxOrder $oOrder */
            $oOrder = $this->getShop()->getNew('oxOrder');

            if ($oOrder->load($this->getEditObjectId())) {
                $this->_oOrder = $oOrder;
            }
        }

        return $this->_oOrder;
    }

    /**
     * Get PayPal Plus payment data object related to current order.
     *
     * @return null|paypPayPalPlusPaymentData
     */
    public function getOrderPayment()
    {
        if (is_null($this->_oOrderPayment)) {

            /** @var paypPayPalPlusPaymentData $oPaymentData */
            $oPaymentData = $this->getShop()->getNew('paypPayPalPlusPaymentData');
            $oOrder = $this->getOrder();

            if (($oOrder instanceof oxOrder) and $oPaymentData->loadByOrderId($oOrder->getId())) {
                $this->_oOrderPayment = $oPaymentData;
            }
        }

        return $this->_oOrderPayment;
    }

    /**
     * Sets an error message in refund processing.
     *
     * @param string $sRefundErrorMessage
     */
    public function setRefundErrorMessage($sRefundErrorMessage)
    {
        $this->_sRefundErrorMessage = (string) $sRefundErrorMessage;
    }

    /**
     * Gets an error message from refund processing.
     *
     * @return string
     */
    public function getRefundErrorMessage()
    {
        return $this->_sRefundErrorMessage;
    }


    /**
     * Get currency code from the related payment data.
     *
     * @return string
     */
    public function getPaymentCurrencyCode()
    {
        return (string) $this->getDataUtils()->invokeGet($this, 'getOrderPayment', 'getCurrency');
    }

    /**
     * Get remaining refunds count for current payment.
     *
     * @return int
     */
    public function getRemainingRefundsCount()
    {
        if (is_null($this->_iRemainingRefunds)) {
            $iMaxRefunds = (int) $this->getShop()->getPayPalPlusConfig()->getMaxNumberRefundsPerPayment();
            $iRefundsAvailable = $iMaxRefunds;
            $oDataAccess = $this->getDataUtils();

            $iRefundsMade = (int) $oDataAccess->invokeGet($this, 'getOrderPayment', 'getRefundsList', 'count');

            if ($iRefundsMade >= $iMaxRefunds) {
                $iRefundsAvailable = 0;
            } elseif ($iRefundsMade > 0) {
                $iRefundsAvailable = $iMaxRefunds - $iRefundsMade;
            }

            $this->_iRemainingRefunds = $iRefundsAvailable;
        }

        return $this->_iRemainingRefunds;
    }

    /**
     * Get maximum possible, remaining payment amount to refund.
     *
     * @return double
     */
    public function getRemainingRefundAmount()
    {
        if (is_null($this->_dRemainingRefundAmount)) {
            $oDataAccess = $this->getDataUtils();
            $oOrderPayment = $this->getOrderPayment();

            $dRemainingRefundAmount = (double) $oDataAccess->invokeGet($oOrderPayment, 'getTotal') -
                                      (double) $oDataAccess->invokeGet($oOrderPayment, 'getTotalAmountRefunded');

            if ($dRemainingRefundAmount < 0.0) {
                $dRemainingRefundAmount = 0.0;
            }

            $this->_dRemainingRefundAmount = round($dRemainingRefundAmount, 2);
        }

        return $this->_dRemainingRefundAmount;
    }


    /**
     * Checks if current order was payed with with a PayPal Plus payment method.
     *
     * @return bool
     */
    public function isPayPalPlusOrder()
    {
        return ($this->getDataUtils()->invokeGet($this, 'getOrder', 'oxorder__oxpaymenttype:$', 'value:$') ===
                $this->getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId());
    }

    /**
     * Check if refund is still possible to perform for current order payment.
     *
     * @return bool
     */
    public function isRefundPossible()
    {
        return (
            $this->isPayPalPlusOrder() and
            (bool) $this->getDataUtils()->invokeGet($this, 'getOrderPayment', 'isRefundable') and
            ($this->getRemainingRefundsCount() > 0) and
            ($this->getRemainingRefundAmount() > 0.0)
        );
    }

    /**
     * Template getter for price formatting.
     * Uses currency code from the related payment data.
     * Applies rounding precision as in PayPal API.
     *
     * @param double|string|int $mPrice
     *
     * @return string
     */
    public function formatPrice($mPrice)
    {
        $oDataConverter = $this->getDataConverter();

        return sprintf(
            '%s <small>%s</small>',
            $oDataConverter->price(round((double) $mPrice, 2)),
            $oDataConverter->string($this->getPaymentCurrencyCode(), 3)
        );
    }


    /**
     * PayPal Plus refund action.
     *
     * Collect and validate user input, load and check related objects and tripper PayPal API refund call.
     * Sets validation of failure errors if any or reloads refunds list on success.
     */
    public function actionRefund()
    {
        $sSaleId = $this->getShop()->getRequestParameter('saleId');

        $sRefundAmount = $this->getShop()->getRequestParameter('refundAmount');
        $sRefundAmount = str_replace(',', '.', $sRefundAmount);

        if ($this->_validateRefundData($sSaleId, $sRefundAmount)) {
            $oDataAccess = $this->getDataUtils();
            $oDataConverter = $this->getDataConverter();
            $oPaymentData = $this->getOrderPayment();

            /** @var paypPayPalPlusRefundHandler $oRefundHandler */
            $oRefundHandler = $this->getShop()->getFromRegistry('paypPayPalPlusRefundHandler');
            $oRefundHandler->init(
                $oDataConverter->price($sRefundAmount),
                $oDataConverter->string($oDataAccess->invokeGet($oPaymentData, 'getCurrency'), 3),
                $oDataConverter->string($oDataAccess->invokeGet($oPaymentData, 'getSaleId'), 32)
            );

            $mReturn = $oRefundHandler->refund($this->getShop()->getPayPalPlusSession()->getApiContext());

            if ($mReturn === true) {
                $this->_forceReload();
            } else {
                $this->_parseRefundError($mReturn);
            }
        }
    }


    /**
     * Validate user input and check if refund could be made.
     *
     * @param string $sSaleId
     * @param string $sRefundAmount
     *
     * @return bool
     */
    protected function _validateRefundData($sSaleId, $sRefundAmount)
    {
        if (!$this->_isSaleIdValid($sSaleId)) {
            return $this->_setError('PAYP_PAYPALPLUS_ERR_INVALID_REQUEST');
        }

        if (!$this->_isAmountAValidNumber($sRefundAmount)) {
            return $this->_setError('PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT');
        }

        if (!$this->_isRefundStillPossible()) {
            return $this->_setError('PAYP_PAYPALPLUS_ERR_REFUND_NOT_POSSIBLE');
        }

        return true;
    }

    /**
     * Check if sale ID belongs to current order payment data.
     *
     * @param string $sSaleId
     *
     * @return bool
     */
    protected function _isSaleIdValid($sSaleId)
    {
        return ($this->getDataUtils()->invokeGet($this, 'getOrderPayment', 'getSaleId') === $sSaleId);
    }

    /**
     * Check if amount is a numeric value greater than zero.
     *
     * @param string $sAmount
     *
     * @return bool
     */
    protected function _isAmountAValidNumber($sAmount)
    {
        if (!is_numeric($sAmount)) {
            return false;
        }

        $dAmount = round((double) $sAmount, 2);

        return ($dAmount > 0.0);
    }

    /**
     * Check if order was payed with PayPal Plus and payment has still refunds left (not exceed maximum refunds count).
     *
     * @return bool
     */
    protected function _isRefundStillPossible()
    {
        return ($this->isPayPalPlusOrder() and ($this->getRemainingRefundsCount() > 0));
    }

    /**
     * Check if an error is a general error code - default message is set, or set it directly otherwise.
     *
     * @param string $sError
     */
    protected function _parseRefundError($sError)
    {
        if ($sError === $this->getShop()->getErrorHandler()->getGeneralErrorCode()) {
            $this->_setError('PAYP_PAYPALPLUS_ERR_REFUND_API_EXCEPTION');
        } else {
            $this->setRefundErrorMessage((string) $sError);
        }
    }

    /**
     * Translate error message by a code and set the error to be displayed.
     *
     * @param string $sErrorCode
     *
     * @return bool
     */
    protected function _setError($sErrorCode)
    {
        $this->setRefundErrorMessage($this->getShop()->translate((string) $sErrorCode, true));

        return false;
    }

    /**
     * Unset preloaded values to force data reload.
     */
    protected function _forceReload()
    {
        $this->_oOrderPayment = null;
        $this->_iRemainingRefunds = null;
        $this->_dRemainingRefundAmount = null;
    }
}
