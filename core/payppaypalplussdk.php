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

// Set eShop timezone as default (to avoid overwriting of the timezone by SDK)
ini_set('date.timezone', date_default_timezone_get());

// Load SDK
require_once dirname(__FILE__) . '/../vendor/autoload.php';


/**
 * Class paypPayPalPlusSdk
 * PayPal REST API SDK driver for OXID eShop.
 */
class paypPayPalPlusSdk extends oxSuperCfg
{

    /**
     * Available SDK API classes to load with magic `new[ApiClass]` calls.
     *
     * @var array
     */
    protected $_aApiAvailableClasses = array(
        'Amount',
        'Address',
        'Details',
        'Item',
        'ItemList',
        'Patch',
        'PatchRequest',
        'Payer',
        'PayerInfo',
        'Payment',
        'PaymentExecution',
        'RedirectUrls',
        'Refund',
        'Sale',
        'ShippingAddress',
        'Transaction',
        'FlowConfig',
        'Presentation',
        'InputFields',
        'WebProfile',
        'Webhook',
        'WebhookEvent',
        'WebhookEventType',
        'WebhookList',
        'WebhookEventList',
        'WebhookEventTypeList',
    );


    /**
     * Magic call method to create and return new SDK API models.
     *
     * Usage examples:
     *   $this->newShippingAddress() -> Creates and returns instance of PayPal\Api\ShippingAddress
     *   $this->newPayer()           -> Creates and returns instance of PayPal\Api\Payer
     *   ...
     *
     * @param string $sName
     * @param array  $aArguments
     *
     * @return null|PayPal\Api\Amount|PayPal\Api\Details|PayPal\Api\Item|PayPal\Api\ItemList|PayPal\Api\Patch|
     *         PayPal\Api\PatchRequest|PayPal\Api\Payer|PayPal\Api\PayerInfo|PayPal\Api\Payment|
     *         PayPal\Api\PaymentExecution|PayPal\Api\RedirectUrls|PayPal\Api\Refund|PayPal\Api\Sale|
     *         PayPal\Api\ShippingAddress|PayPal\Api\Transaction
     */
    public function __call($sName, $aArguments)
    {
        $oShop = paypPayPalPlusShop::getShop();

        /** @var paypPayPalPlusSuperCfg $oSuperCfg */
        $oSuperCfg = $oShop->getFromRegistry('paypPayPalPlusSuperCfg');

        $mReturn = null;
        $mClassToCall = $oSuperCfg->parseCallFor('new', $sName);

        if (is_null($mClassToCall)) {
            return $mReturn;
        }

        if (in_array($mClassToCall, $this->_aApiAvailableClasses)) {
            $sClassFullName = 'PayPal\Api\\' . $mClassToCall;
            $mReturn = new $sClassFullName();
        }

        return $mReturn;
    }


    /**
     * Get a configuration parameters array for PayPal SDK.
     * It uses PayPal Plus module configuration values to fill the array and some static value.
     *
     * @param bool $blAuthDataOnly If True only client ID and secret values are returned, if False - all config data.
     *
     * @return array
     */
    public function getSdkConfig($blAuthDataOnly = false)
    {
        $oModuleConfig = paypPayPalPlusShop::getShop()->getPayPalPlusConfig();

        if (!empty($blAuthDataOnly)) {

            // Only Client ID and Secret values returned (for a token object as example)
            return array($oModuleConfig->getClientId(), $oModuleConfig->getSecret());
        }

        $sLogPath = $oModuleConfig->getSetting('sShopDir') . DIRECTORY_SEPARATOR .
                    'log' . DIRECTORY_SEPARATOR .
                    (string) $oModuleConfig->getModuleSetting('LogFile', 'paypalplus.log');

        // Return ful set of SDK configuration parameters
        return array(
            'http.ConnectionTimeOut'                     => (int) $oModuleConfig->getModuleSetting('Timeout', 60),
            'http.Retry'                                 => (int) $oModuleConfig->getModuleSetting('Retry', 1),
            'http.headers.PayPal-Partner-Attribution-Id' => 'Oxid_Cart_6Cart_Plus',
            'mode'                                       => $oModuleConfig->getMode(),
            'service.EndPoint'                           => $oModuleConfig->getBaseUri(),
            'log.LogEnabled'                             => (bool) $oModuleConfig->getModuleSetting('LogEnabled'),
            'log.FileName'                               => $sLogPath,
            'log.LogLevel'                               => (string) $oModuleConfig->getModuleSetting('LogLevel'),
            'validation.level'                           => (string) $oModuleConfig->getModuleSetting('Validation'),
            'cache.enabled'                              => 'TRUE',
            'cache.FileName'                             =>  (string) $oModuleConfig->getSetting('sCompileDir') .'/auth.cache',
            //'date.timezone' => date_default_timezone_get(), //todo: enable when it is supported by SDK
            // todo (nice2have): cache configuration: cache.enabled and cache.FileName
        );
    }

    /**
     * Get PayPal API token credentials instance.
     *
     * @return PayPal\Auth\OAuthTokenCredential
     */
    public function newTokenCredential()
    {
        list($sClientId, $sSecret) = $this->getSdkConfig(true);

        return new PayPal\Auth\OAuthTokenCredential($sClientId, $sSecret);
    }

    /**
     * Get API Context instance.
     * It is returned initialized with an API token and module settings.
     *
     * @param PayPal\Auth\OAuthTokenCredential $oCredentials
     *
     * @return PayPal\Rest\ApiContext
     */
    public function newApiContext(PayPal\Auth\OAuthTokenCredential $oCredentials)
    {
        $oApi = new PayPal\Rest\ApiContext($oCredentials);
        $oApi->setConfig($this->getSdkConfig());

        return $oApi;
    }
}
