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
 * Class paypPayPalPlusWall.
 * PayPal Plus Wall rendering widget.
 */
class paypPayPalPlusWall extends oxWidget
{

    /**
     * Widget template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'payppaypalpluswall.tpl';

    /**
     * OXID eShop wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * Payment instance.
     *
     * @var null|PayPal\Api\Payment
     */
    protected $_oPayment = null;

    /**
     * A part of URL with a controller and action data for external payment methods to redirect to.
     * NOTE: Payment ID is appended dynamically in JavaScript, and prefix is fetched from active shop config.
     *
     * @var string
     */
    protected $_sExternalMethodsUri = 'cl=payment&fnc=routePayment&paymentid=';


    /**
     * Overloaded parent method.
     * Get created PayPal Payment instance from session and set it as widget property.
     * Also set eShop wrapper helper.
     */
    public function init()
    {
        $this->_oShop = paypPayPalPlusShop::getShop();

        if ($this->getShop()->getValidator()->isPaymentCreated()) {
            $this->_oPayment = $this->getShop()->getPayPalPlusSession()->getPayment();
        }

        $this->_paypPayPalPlusWall_init_parent();
    }

    /**
     * Returns if view should be cached.
     *
     * @return bool
     */
    public function isCacheable()
    {
        return false;
    }


    /**
     * Get OXID eShop wrapper instance.
     *
     * @return null|paypPayPalPlusShop
     */
    public function getShop()
    {
        return $this->_oShop;
    }

    /**
     * Get PayPal Plus JavaScript Library URL.
     *
     * @return string
     */
    public function getPayPalPlusLibraryUrl()
    {
        return $this->getShop()->getPayPalPlusConfig()->getPayPalPlusJsUri();
    }

    /**
     * Get payment approval URL from created payment instance.
     *
     * @return array
     */
    public function getApprovalUrl()
    {
        /** @var paypPayPalPlusPaymentHandler $oPayPalPaymentHandler */
        $oPayPalPaymentHandler = $this->getShop()->getFromRegistry('paypPayPalPlusPaymentHandler');
        $oPayPalPaymentHandler->setPayment($this->_oPayment);

        return $oPayPalPaymentHandler->getApprovalUrl();
    }

    /**
     * Get PayPal API mode: sandbox or live.
     * Note: it's lowercase for the wall integration.
     *
     * @return string
     */
    public function getApiMode()
    {
        $oShop = $this->getShop();

        return $oShop->getStr()->strtolower($oShop->getPayPalPlusConfig()->getMode());
    }

    /**
     * Get language as locale code, for example "de_DE".
     * Default to de_DE if it is not set in admin language settings
     *
     * @return string
     */
    public function getLanguageCode()
    {
        $sCurrentLanguageAbbr = $this->getShop()->getLang()->getLanguageAbbr();
        $aLanguageParams = $this->getShop()->getConfig()->getConfigParam('aLanguageParams');

        //check if locale code is set in the admin panel and not empty, administrator is responsible for locale code validity
        if (!empty($aLanguageParams[$sCurrentLanguageAbbr]['payppaypalplus_localecode'])) {

            return $aLanguageParams[$sCurrentLanguageAbbr]['payppaypalplus_localecode'];
        }

        return 'de_DE';
    }

    /**
     * Get user billing country code. Defaults to US.
     *
     * @return string
     */
    public function getCountryCode()
    {
        $mUserCountryCode = $this->getShop()->getUser()->getUserCountryCode();

        return is_null($mUserCountryCode) ? 'US' : $mUserCountryCode;
    }

    /**
     * Get translated error message for a default failure case (payment validation).
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->getShop()->translate('PAYP_PAYPALPLUS_ERROR_NOPAYMENT');
    }

    /**
     * Get general error code to trigger the default failure error message.
     *
     * @return string
     */
    public function getGeneralErrorCode()
    {
        return $this->getShop()->getErrorHandler()->getGeneralErrorCode();
    }

    /**
     * Get an ID of external button element.
     *
     * @return string
     */
    public function getExternalButtonId()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('NextButtonId');
    }

    /**
     * Get a selector for top navigation next step link on checkout payment page.
     *
     * @return string
     */
    public function getNextStepLink()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('NextLink');
    }

    /**
     * Get a selector for a parent element of the top navigation next step link on checkout payment page.
     *
     * @return string
     */
    public function getNextStepLinkParent()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('NextLinkParent');
    }

    /**
     * Get a selector for a default radio buttons (for payment methods selection) on checkout payment page.
     *
     * @return string
     */
    public function getPaymentRadioButton()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('PaymentRadio');
    }

    /**
     * Get a selector for a payment methods list item on checkout payment page.
     *
     * @return string
     */
    public function getPaymentListItem()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('ListItem');
    }

    /**
     * Get a selector for a title element of the payment methods list item on checkout payment page.
     *
     * @return string
     */
    public function getPaymentListItemTitle()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('ListItemTitle');
    }

    /**
     * Get a format string of selector for payment method label element on checkout payment page.
     * Notice, that if "%s" is used inside the format string, it is replaced by a dynamic value - payment method ID.
     *
     * @return string
     */
    public function getPaymentLabelFormat()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('LabelFormat');
    }

    /**
     * Get a selector for a child element of the payment method label on checkout payment page.
     *
     * @return string
     */
    public function getPaymentLabelChild()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('LabelChild');
    }

    /**
     * Get a selector of the payment method description tag on checkout payment page.
     *
     * @return string
     */
    public function getPaymentDescription()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('Description');
    }

    /**
     * Get a prefix for a payment method ID attribute used in input elements on checkout payment page.
     *
     * @return string
     */
    public function getPaymentIdPrefix()
    {
        return (string) $this->getShop()->getPayPalPlusConfig()->getModuleSetting('MethodIdPrefix');
    }

    /**
     * Get a hash key to use for AJAX response verification.
     *
     * @return string
     */
    public function getAjaxResponseToken()
    {
        return $this->getShop()->getPayPalPlusConfig()->getInternalTransactionToken();
    }

    /**
     * Get a list of external payment methods that should get into the PayPal Plus wall.
     *
     * @return array
     */
    public function getExternalMethods()
    {
        return (array) $this->getShop()->getPayPalPlusConfig()->getExternalMethods();
    }

    /**
     * Get a base URL to redirect external (3rd party) payment methods to.
     * NOTE: Payment ID is appended dynamically in JavaScript.
     *
     * @return string
     */
    public function getExternalMethodsRedirectUrl()
    {
        return $this->getShop()->getPayPalPlusConfig()->getShopBaseLink() . $this->_sExternalMethodsUri;
    }

    /**
     * Get if template integration validation is needed
     *
     * @return bool
     */
    public function isTemplateValidationNeeded()
    {
        return (bool) $this->getShop()->getPayPalPlusConfig()->isTemplateValidationNeeded();
    }

    /**
     * Get if we are under Mobile template
     *
     * @return bool
     */
    public function isMobile()
    {
        return (bool) $this->getShop()->getPayPalPlusConfig()->isMobile();
    }

    /**
     * Get if we are under Flow theme
     *
     * @return bool
     */
    public function isFlow()
    {
        return (bool) $this->getShop()->getPayPalPlusConfig()->isFlowTheme();
    }

    /**
     * Parent `init` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusWall_init_parent()
    {
        parent::init();
    }
}
