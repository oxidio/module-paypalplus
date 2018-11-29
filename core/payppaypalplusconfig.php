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
 * Class paypPayPalPlusConfig
 * Module configuration helper.
 */
class paypPayPalPlusConfig extends paypPayPalPlusSuperCfg
{

    /**
     * PayPal Plus payment method ID in OXID eShop database.
     *
     * @var string
     */
    protected $_sPayPalPlusMethodId = 'payppaypalplus';

    /**
     * A prefix for all module settings keys.
     *
     * @var string
     */
    protected $_sModuleSettingsPrefix = 'paypPayPalPlus';

    /**
     * A flag for mobile version of website
     *
     * @var null|boolean
     */
    protected $_blIsMobile = null;

    /**
     * A flag for flow theme
     *
     * @var null|boolean
     */
    protected $_blIsFlowTheme = null;

    /**
     * A prefix for Mobile theme settings
     *
     * @var string
     */
    protected $_sMobilePrefix = 'Mob';

    /**
     * A prefix for Flow theme settings
     *
     * @var string
     */
    protected $_sFlowPrefix = 'Flow';

    /**
     * Second prefix for sandbox mode module settings.
     *
     * @var string
     */
    protected $_sSandboxSettingsPrefix = 'Sandbox';

    /**
     * Sandbox mode name for PayPal REST SDK..
     *
     * @var string
     */
    protected $_sModeNameSandbox = 'SANDBOX';

    /**
     * Live mode name for PayPal REST SDK.
     *
     * @var string
     */
    protected $_sModeNameLive = 'LIVE';

    /**
     * PayPal API base URI.
     *
     * @var string
     */
    protected $_sBaseUri = 'https://api.paypal.com';

    /**
     * PayPal sandbox API base URI.
     *
     * @var string
     */
    protected $_sSandboxBaseUri = 'https://api.sandbox.paypal.com';

    /**
     * PayPal Plus JavaScript Library URI.
     *
     * @var string
     */
    protected $_sPayPalPlusJsUri = 'https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js';

    /**
     * Sandbox mode flag.
     *
     * @var null|bool
     */
    protected $_blIsSandboxMode = null;

    /**
     * IDs of standard OXID payment methods which could NOT be displayed in PayPal Plus wall.
     *
     * @var array
     */
    protected $_aExternalMethodsExceptions = array('oxiddebitnote', 'oxidcreditcard');

    /**
     * A status code of successful payment (sale instance).
     * Payment with such status could also be refunded.
     *
     * @var string
     */
    protected $_sRefundablePaymentStatus = 'completed';

    /**
     * A status code of successfully executed payment.
     *
     * @var string
     */
    protected $_sExecutedPaymentSuccessStatus = 'approved';

    /**
     * The state code of a pending sale transaction as it is sent from PayPal
     *
     * @var string
     */
    protected $_sTransactionPendingState = 'pending';

    /**
     * Maximum number of refunds allowed per payment.
     *
     * @var int
     */
    protected $_iMaxRefundsPerPayment = 10;

    /**
     * Request parameter marking successful return from PayPal page.
     *
     * @var string
     */
    protected $_sSuccessfulReturnParameter = 'payppaypalplussuccess';

    /**
     * Request parameter marking payment cancellation on PayPal page.
     *
     * @var string
     */
    protected $_sCancellationReturnParameter = 'payppaypalpluscancel';

    /**
     * Request parameter for a payment method ID to be preselected (forced).
     *
     * @var string
     */
    protected $_sForcedPaymentParameter = 'force_paymentid';

    /**
     * Request parameter returned by the PayPal API to identify payment instance.
     *
     * @var string
     */
    protected $_sPayPalPaymentIdParameter = 'paymentId';

    /**
     * Request parameter returned by the PayPal API to identify payer.
     * Used for confirmed payment execution.
     *
     * @var string
     */
    protected $_sPayPalPayerIdParameter = 'PayerID';

    /**
     * PayPal Plus SDK Payment object intent value.
     *
     * @var string
     */
    protected $_sPaymentIntent = 'sale';

    /**
     * PayPal Plus SDK Payer object payment method value.
     *
     * @var string
     */
    protected $_sPayerPaymentMethod = 'paypal';

    /**
     * Price values decimal part separator for PayPal API.
     *
     * @var string
     */
    protected $_sDecimalsSeparator = '.';

    /**
     * Price values thousands separator for PayPal API.
     *
     * @var string
     */
    protected $_sThousandsSeparator = '';

    /**
     * Event types that the webhook should listen to.
     * The event types currently supported by PayPal are as follows:
     *
     *   PAYMENT.AUTHORIZATION.CREATED: This event gets triggered when an authorization happens.
     *          This is when the payment authorization is created, approved, and executed.
     *          The other use case is when a future payment authorization is created.
     *   PAYMENT.AUTHORIZATION.VOIDED: This event gets triggered when an authorization is voided.
     *   PAYMENT.CAPTURE.COMPLETED: This event gets triggered when a capture is completed.
     *   PAYMENT.CAPTURE.PENDING: This event gets triggered when a capture goes into pending state.
     *   PAYMENT.CAPTURE.REFUNDED: This event gets triggered when a capture is refunded by the merchant.
     *   PAYMENT.CAPTURE.REVERSED: This event gets triggered when a capture is reversed by PayPal.
     *   PAYMENT.SALE.COMPLETED: This event gets triggered when the sale is completed.
     *   PAYMENT.SALE.PENDING: This event gets triggered when a sale goes into pending state.
     *   PAYMENT.SALE.REFUNDED: This event gets triggered when the sale is refunded by the merchant.
     *   PAYMENT.SALE.REVERSED: This event gets triggered when the sale is reversed by PayPal.
     *   RISK.DISPUTE.CREATED: This event gets triggered when a dispute is created.
     *
     * @param $sEventType
     */
    protected $_aSubscribedEventTypes = array('PAYMENT.SALE.COMPLETED', 'PAYMENT.SALE.PENDING', 'PAYMENT.SALE.REFUNDED', 'PAYMENT.SALE.REVERSED',);


    protected $_sPaymentOverInvoiceInstructionType = 'PAY_UPON_INVOICE';

    /**
     * Integration template validation needed flag
     *
     * @var null|bool
     */
    protected $_blValidateTemplate = null;

    /**
     * Magic call method to implement class properties getter.
     * It expects a get call of a string property.
     *
     * Examples:
     *  $this->getSuccessfulReturnParameter() -> would return a value of property "_sSuccessfulReturnParameter"
     *  $this->getDecimalsSeparator()         -> would return a value of property "_sDecimalsSeparator"
     *  ...
     *
     * @param string $sName
     * @param array  $aArguments
     *
     * @return null|string
     */
    public function __call($sName, $aArguments)
    {
        $mReturn = null;
        $mSettingName = $this->parseCallFor('get', $sName);

        if (is_null($mSettingName)) {
            return $mReturn;
        }

        $sPropertyName = '_s' . (string) $mSettingName;

        if (property_exists($this, $sPropertyName)) {
            $mReturn = (string) $this->$sPropertyName;
        }

        $sPropertyName = '_a' . (string) $mSettingName;

        if (property_exists($this, $sPropertyName)) {
            $mReturn = (array) $this->$sPropertyName;
        }

        return $mReturn;
    }


    /**
     * Check if PayPal Plus is in sandbox mode.
     *
     * @return bool
     */
    public function isSandbox()
    {
        if (is_null($this->_blIsSandboxMode)) {
            $this->_blIsSandboxMode = (bool) $this->getModuleSetting('Sandbox');
        }

        return $this->_blIsSandboxMode;
    }

    /**
     * Check if Website runs in Mobile mode
     *
     * @return bool
     */
    public function isMobile()
    {
        if (is_null($this->_blIsMobile)) {
            if (class_exists('oeThemeSwitcherThemeManager')) {
                $oThemeManager = $this->getShop()->getFromRegistry('oeThemeSwitcherThemeManager');
                $this->_blIsMobile = (bool) $oThemeManager->isMobileThemeRequested();
            } else {
                $this->_blIsMobile = false;
            }
        }

        return $this->_blIsMobile;
    }

    /**
     * Check if Website runs in Flow theme
     *
     * @return bool
     */
    public function isFlowTheme()
    {
        if (is_null($this->_blIsFlowTheme)) {
            if (trim(strtolower($this->getShop()->getConfig()->getConfigParam('sTheme'))) == 'flow') {
                $this->_blIsFlowTheme = true;
            } else {
                $this->_blIsFlowTheme = false;
            }
        }

        return $this->_blIsFlowTheme;
    }

    /**
     * Get a name of current mode (sandbox or live).
     *
     * @return string
     */
    public function getMode()
    {
        return $this->isSandbox() ? $this->_sModeNameSandbox : $this->_sModeNameLive;
    }

    /**
     * Get PayPal API base URI for a current mode.
     *
     * @return string
     */
    public function getBaseUri()
    {
        return $this->isSandbox() ? $this->_sSandboxBaseUri : $this->_sBaseUri;
    }

    /**
     * Get a maximum number of possible refunds for a payment.
     *
     * @return int
     */
    public function getMaxNumberRefundsPerPayment()
    {
        return $this->_iMaxRefundsPerPayment;
    }


    /**
     * Get shop configuration parameter value.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getSetting($sKey)
    {
        return $this->getShop()->getSetting($sKey);
    }

    /**
     * Get module configuration parameter by key (key with no module prefix).
     * Try to get Mobile template setting if we are under Mobile Template
     *
     * @param string $sKey              Module settings key wih no prefix.
     * @param mixed  $mAlternativeValue Alternative (default) value to use if setting is empty.
     *
     * @return mixed
     */
    public function getModuleSetting($sKey, $mAlternativeValue = null)
    {
        if ($this->isMobile()) {
            $mValue = $this->getSetting($this->_sModuleSettingsPrefix . $this->_sMobilePrefix . $sKey);
        }

        if ($this->isFlowTheme()) {
            $mValue = $this->getSetting($this->_sModuleSettingsPrefix . $this->_sFlowPrefix . $sKey);
        }

        if (is_null($mValue)) {
            $mValue = $this->getSetting($this->_sModuleSettingsPrefix . $sKey);
        }

        if (!is_null($mAlternativeValue) and empty($mValue)) {
            $mValue = $mAlternativeValue;
        }

        return $mValue;
    }

    /**
     * Get API client ID for a current mode.
     *
     * @return string
     */
    public function getClientId()
    {
        return (string) $this->_getModeDependantModuleSetting('ClientId');
    }

    /**
     * Get API secret key value for a current mode.
     *
     * @return string
     */
    public function getSecret()
    {
        return (string) $this->_getModeDependantModuleSetting('Secret');
    }

    /**
     * Get a hash key for internal module request/response verification.
     *
     * @todo (nice2have): Make it more secure, based on configurable salt key and payment data.
     *
     * @return string
     */
    public function getInternalTransactionToken()
    {
        return md5(date('Y-m-d') . (string) session_id());
    }

    /**
     * Get a list of counties available for PayPal Plus.
     *
     * @todo: Later there might be more countries. Maybe make it configurable in module settings.
     *
     * @return array
     */
    public function getPayPalPlusCounties()
    {
        return array('DE');
    }

    /**
     * Get configured IDs of payment methods, which should be displayed inside PayPal Plus wall widget.
     *
     * @return array
     */
    public function getConfiguredExternalMethods()
    {
        return (array) $this->getModuleSetting('ExternalMethods');
    }

    /**
     * Get IDs of standard OXID payment methods which could NOT be displayed in PayPal Plus wall.
     *
     * @return array
     */
    public function getExternalMethodsExceptions($blIncludingThePayPalPlus = true)
    {
        $aExternalMethodsExceptions = (array) $this->_aExternalMethodsExceptions;

        if (!empty($blIncludingThePayPalPlus)) {
            $aExternalMethodsExceptions = array_merge(
                $aExternalMethodsExceptions,
                array((string) $this->getPayPalPlusMethodId())
            );
        }

        return $aExternalMethodsExceptions;
    }

    /**
     * Get IDs of payment methods, which should be displayed inside PayPal Plus wall widget.
     * Excludes exceptions from configured IDs.
     *
     * @return array
     */
    public function getExternalMethods()
    {
        return array_diff($this->getConfiguredExternalMethods(), $this->getExternalMethodsExceptions());
    }

    /**
     * Get a clean base URL of an active (sub-)shop suitable to pass to PayPal API.
     *
     * @return string
     */
    public function getShopBaseLink()
    {
        return (string) str_replace('&amp;', '&', $this->getShop()->getConfig()->getShopSecureHomeURL());
    }

    /**
     * Check if PayPal Plus needs to Validate Template integration
     *
     * @return bool
     */
    public function isTemplateValidationNeeded()
    {
        if (is_null($this->_blValidateTemplate)) {
            $this->_blValidateTemplate = (bool) $this->getModuleSetting('ValidateTemplate');
        }

        return $this->_blValidateTemplate;
    }

    /**
     * Get Module settings prefix
     *
     * @return string
     */
    public function getModuleSettingsPrefix()
    {
        return $this->_sModuleSettingsPrefix;
    }

    /**
     * Get module configuration parameter value for settings that vary for sandbox and live modes.
     *
     * @param string $sKey Module settings key with no prefixes.
     *
     * @return mixed
     */
    protected function _getModeDependantModuleSetting($sKey)
    {
        if ($this->isSandbox()) {
            $sKey = $this->_sSandboxSettingsPrefix . $sKey;
        }

        return $this->getModuleSetting($sKey);
    }
}
