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
 * Class paypPayPalPlusPayment.
 * Overloads Payment controller.
 *
 * @see Payment
 */
class paypPayPalPlusPayment extends paypPayPalPlusPayment_parent
{

    /**
     * TODO add description here
     */
    const PARAM_PAYPAL_PLUS_PAYMENT_IS_ACTIVE = 'pppMethodActive';

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * A controller name returned on validation success.
     *
     * @var string
     */
    protected $_sSuccessControllerName = 'order';

    /**
     * A name of a payment ID request/session parameter.
     *
     * @var string
     */
    protected $_sPaymentIdParameterKey = 'paymentid';


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
     * Get a name of controller that stands for payment validation success (next step name).
     *
     * @return string
     */
    public function getSuccessControllerName()
    {
        return $this->_sSuccessControllerName;
    }

    /**
     * Get a name of request/session parameter that stores selected payment method ID.
     *
     * @return string
     */
    public function getPaymentIdParameterName()
    {
        return $this->_sPaymentIdParameterKey;
    }


    /**
     * Overloaded parent method.
     * Create a new PayPal Plus Payment instance if it is not yet created or is outdated.
     * Also creates it on errors and payment cancellation.
     *
     * @return mixed
     */
    public function render()
    {
        $oShop = $this->getShop();

        // Validate special cases to inform user about possible problems
        $this->_shopDataValidation();

        // Make sure payment is not yet approved, otherwise need to reset it on this step
        $this->_checkPaymentStatus();

        // Check for errors
        $sPaymentErrorKey = $oShop->getErrorHandler()->getPaymentErrorKey();
        $blPaymentError = (bool) $oShop->getSessionVariable($sPaymentErrorKey);

        // Check for payment cancellation parameter.
        $blOrderCancelled = (bool) $oShop->getRequestParameter(
            $oShop->getPayPalPlusConfig()->getCancellationReturnParameter()
        );

        if ($this->_shouldPaymentBeCreated() or $blPaymentError or $blOrderCancelled) {
            $this->_createPayment();
        }

        return $this->_paypPayPalPlusPayment_render_parent();
    }

    /**
     * Overloaded parent method.
     * If payment was valid according with OXID standard validation, check it and update for PayPal API.
     *
     * @inheritDoc
     */
    public function validatePayment()
    {
        $mReturn = $this->_paypPayPalPlusPayment_validatePayment_parent();

        if ($mReturn === $this->getSuccessControllerName()) {

            // If payment is valid in general, perform additional checks and update PayPal payment
            $oShop = $this->getShop();
            $sPaymentId = $oShop->getRequestOrSessionParameter($this->getPaymentIdParameterName());

            //PayPal Plus specific block
            if ($sPaymentId === $oShop->getPayPalPlusConfig()->getPayPalPlusMethodId()) {
                // Force payment PayPal Plus to recalculate surcharge if needed
                $this->_setPayment($sPaymentId);

                // Call for payment update
                //Expect for validation exceptions
                try {
                    $this->_triggerPaymentSpecificValidation();
                } catch ( oxException $oException ) {
                    $this->_handlePaymentPaymentValidationErrors($oException);
                }
                $this->_updatePayment();
            }
        }

        return $mReturn;
    }

    /**
     * {@inheritdoc}
     *
     * Translates the error test in the current shop language
     *
     * @return string
     */
    public function getPaymentErrorText()
    {
        $sPaymentErrorText = parent::getPaymentErrorText();
        $sMessage =  oxRegistry::getLang()->translateString($sPaymentErrorText);
        return $sMessage;
    }

    /**
     * Execute payment specific user data validation. Throws errors; Method chain supported.
     *
     * @throws oxException
     */
    protected function _triggerPaymentSpecificValidation()
    {
        //PayPalPlus and PayPal specific block
        if ( oxRegistry::getConfig()->getRequestParameter( static::PARAM_PAYPAL_PLUS_PAYMENT_IS_ACTIVE ) ) {
            /** @var paypPayPalPlusPaymentHandler $oPayPalPaymentHandler */
            $oPayPalPaymentHandler = paypPayPalPlusShop::getShop()->getFromRegistry( 'paypPayPalPlusPaymentHandler' );
            $oPayPalPaymentHandler->validateUserData();
        }
        return $this;
    }

    /**
     * Uniform way to handle payment validation errors.
     * Methods responds message for AJAX request.
     * Method chain supported.
     *
     * @param oxException $oException
     *
     * @return self
     */
    protected function _handlePaymentPaymentValidationErrors(oxException $oException)
    {
        $oErrorHandler = $this->getShop()->getErrorHandler();
        $oErrorHandler->debug( $oException );
        $sMessage =  oxRegistry::getLang()->translateString($oException->getMessage());
        $this->_ajaxRespondWithMessage( $sMessage );
        return $this;
    }

    /**
     * PayPal Plus payment methods router.
     * For external (3rd party methods) (default eShop methods) it checks is method is configured and valid and
     * redirects to order overview page forcing the method to be selected.
     */
    public function routePayment()
    {
        $oShop = $this->getShop();
        $sPaymentIdParameterName = $this->getPaymentIdParameterName();
        $sForcedPaymentIdParameterName = 'force_' . $sPaymentIdParameterName;
        $sReturnController = $this->getSuccessControllerName();

        $sPaymentMethodId = $oShop->getRequestParameter($sPaymentIdParameterName);
        $aValidMethods = (array) $oShop->getPayPalPlusConfig()->getExternalMethods();

        if (!in_array($sPaymentMethodId, $aValidMethods)) {
            $oShop->getErrorHandler()->setPaymentErrorAndRedirect();
        }

        $sRedirectUrl = $oShop->getConfig()->getShopCurrentUrl() . '&cl=' . $sReturnController . '&' .
                        $sForcedPaymentIdParameterName . '=' . $sPaymentMethodId;

        $oShop->getUtils()->redirect($sRedirectUrl);

        return $sReturnController;
    }


    /**
     * Verify shop data to inform user about possible problems:
     *  - Checks if shipping address state is not missing in case of country is United States.
     */
    protected function _shopDataValidation()
    {
        $oShop = $this->getShop();

        /** @var paypPayPalPlusUserData $oUserData */
        $oUserData = $oShop->getNew('paypPayPalPlusUserData');

        if ($oUserData->getShippingAddressValueCountryCode() === 'US' and !$oUserData->getShippingAddressValueState()) {
            $oShop->getErrorHandler()->setDataValidationNotice();
        }
    }

    /**
     * Reset all session if there is an approved payment.
     * On this step user will anyway go through a new payment creation or select another method.
     */
    protected function _checkPaymentStatus()
    {
        $oPayPalSession = $this->getShop()->getPayPalPlusSession();

        if ($oPayPalSession->getApprovedPayment()) {
            $oPayPalSession->reset();
        }
    }

    /**
     * Check if payment needs to be created.
     * Condition is that ot does not exist yet exists or is not up to date.
     *
     * @return bool
     */
    protected function _shouldPaymentBeCreated()
    {
        $oShop = $this->getShop();

        $oValidator = $oShop->getValidator();
        $oPayPalSession = $oShop->getPayPalPlusSession();

        $oBasket = $oShop->getBasket();
        $sCurrentBasketStamp = $oBasket->getBasketHash();

        return (
            (!$oValidator->isPaymentCreated() or ($oPayPalSession->getBasketStamp() !== $sCurrentBasketStamp)) and
            $oValidator->isPaymentPossible() or $oBasket->getUpdateNeeded()
        );
    }

    /**
     * Set PayPal Plus as current payment method, create and store to session PayPal Payment instance.
     *
     * @todo (nice2have): Check if same workflow is used many times and consider creating one helper to: create and set payment to session.
     */
    protected function _createPayment()
    {
        $oShop = $this->getShop();

        $this->_setPayment($oShop->getPayPalPlusConfig()->getPayPalPlusMethodId());

        $oPayPalSession = $oShop->getPayPalPlusSession();

        $sInvoiceNumber = $this->_getInvoiceNumber($oPayPalSession);

        /** @var paypPayPalPlusPaymentHandler $oPayPalPaymentHandler */
        $oPayPalPaymentHandler = $oShop->getFromRegistry('paypPayPalPlusPaymentHandler');
        $oPayPalPaymentHandler->setInvoiceNumber($sInvoiceNumber);
        $oPayPalPaymentHandler->init();
        $oPayPalPaymentHandler->create($oPayPalSession->getApiContext());

        $oPayPalSession->setPayment($oPayPalPaymentHandler->getPayment());
        $oPayPalSession->setBasketStamp($oShop->getBasket()->getBasketHash());
    }

    /**
     * Set payment method ID to the controller, session and update it in basket if not yet set.
     * Recalculated basket for payment surcharge if it is incorrect.
     *
     * @todo (nice2have): Check if same workflow is used many times and consider creating one helper to: set method by ID.
     *
     * @param $sPaymentId
     */
    protected function _setPayment($sPaymentId)
    {
        $blRecalculateBasket = false;
        $oShop = paypPayPalPlusShop::getShop();

        $oBasket = $oShop->getBasket();
        $oPaymentCost = $oBasket->getPaymentCost();

        /** @var oxPayment $oPayment */
        $oPayment = $oShop->getNew('oxPayment');
        $oPayment->load($sPaymentId);
        $oPayment->calculate($oBasket);
        $oPaymentPrice = $oPayment->getPrice();

        if (($oPaymentPrice instanceof oxPrice) and
            ($oPaymentCost instanceof oxPrice) and
            ($oPaymentPrice->getPrice() !== $oPaymentCost->getPrice())
        ) {
            $blRecalculateBasket = true;
        }

        /**
         * Did the user request Trusted Shop Protection?
         * If so, update the basket so that it fills the value for basket::_aCosts['oxtsprotection'], which is retrieved later by the module.
         *
         * Actually this is probably a bug in the shop, that parent::validatePayment() does not update the costs for TsProtection.
         * oxBasket::calculateBasket does not update the TS Cost without calling oxBasket::setTsProductId prior to the update
         *
         * We retrieve the value from the SESSION and not from the Request parameter as the session is what counts later on.
         *
         * @see \paypPayPalPlusOxBasket::getTsProductId
         *
         * @var  $blTsProtection
         */
        $sTsProtection = $oShop->getSessionVariable('stsprotection');
        if (!empty($sTsProtection)) {
            /**  Cannot be tested on a  vanilla OXID Testshop as Trusted Shops has to be configured  */
            // @codeCoverageIgnoreStart
            $blRecalculateBasket = true;
            // @codeCoverageIgnoreEnd
        }

        if ($blRecalculateBasket || $oBasket->getUpdateNeeded()) {
            $oBasket->setPayment($sPaymentId);
            $oBasket->calculateBasket(true);
            $oShop->setBasket($oBasket);
        }
    }

    /**
     * Check if PayPal payment was created and update it with user data.
     * On successful update respond with AJAX success token.
     */
    protected function _updatePayment()
    {
        $oShop = paypPayPalPlusShop::getShop();

        if ($oShop->getValidator()->isPaymentCreated() and $oShop->getRequestParameter('ajax')) {
            $oPayPalSession = $oShop->getPayPalPlusSession();
            $oPayment = $oPayPalSession->getPayment();

            $sInvoiceNumber = $this->_getInvoiceNumber($oPayPalSession);

            /** @var paypPayPalPlusPaymentHandler $oPayPalPaymentHandler */
            $oPayPalPaymentHandler = $oShop->getFromRegistry('paypPayPalPlusPaymentHandler');
            $oPayPalPaymentHandler->setPayment($oPayment);
            $oPayPalPaymentHandler->setInvoiceNumber($sInvoiceNumber);
            $oPayPalPaymentHandler->update($oPayPalSession->getApiContext());

            $oPayPalSession->setPayment($oPayPalPaymentHandler->getPayment());

            // Send success response
            $this->_ajaxResponseSuccess();
        } else {
            $oShop->getErrorHandler()->setPaymentErrorAndRedirect(5);
        }
    }


    /**
     * Respond with success token.
     *
     * @codeCoverageIgnore
     */
    protected function _ajaxResponseSuccess()
    {
        $this->_ajaxRespondWithMessage(paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getInternalTransactionToken());
    }

    /**
     * @param string $sResponseMessage
     */
    protected function _ajaxRespondWithMessage( $sResponseMessage)
    {
        exit($sResponseMessage);
    }

    /**
     * Parent `render` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _paypPayPalPlusPayment_render_parent()
    {
        return parent::render();
    }

    /**
     * Parent `validatePayment` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _paypPayPalPlusPayment_validatePayment_parent()
    {
        return parent::validatePayment();
    }

    /**
     * @param $oPayPalSession
     *
     * @return bool
     */
    protected function _getInvoiceNumber($oPayPalSession)
    {
        $sInvoiceNumber = '';

        $blTransmitInvoiceNumber = $this->getConfig()->getConfigParam('paypPayPalPlusInvNr');
        if ($blTransmitInvoiceNumber &&
            !$sInvoiceNumber = $oPayPalSession->getInvoiceNumber()) {
            $oOrder = oxNew('oxOrder');
            $sInvoiceNumber = $oOrder->getNextOrderNr();
            $oPayPalSession->setInvoiceNumber($sInvoiceNumber);

            return $sInvoiceNumber;
        }

        return $sInvoiceNumber;
    }
}
