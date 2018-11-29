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
 * Class paypPayPalPlusEvents.
 * Module activation/deactivation event actions handler.
 */
class paypPayPalPlusEvents extends paypPayPalPlusSuperCfg
{

    /**
     * A primary key value of PayPal Plus payment method in eShop database.
     *
     * @var string
     */
    protected $_sPayPalPlusMethodId = null;

    /**
     * PayPal API context
     *
     * @var null|\PayPal\Rest\ApiContext
     */
    protected $_oPayPalApiContext = null;


    /**
     * Actions sequence for module activation event.
     */
    public function activate()
    {
        // Install new database tables and fields
        $this->_createDatabaseTables();

        if (!$this->_isInstalled()) {
            // Install new payment method and configure it to work out-of-the-box.
            $this->_createPaymentMethod();
            $this->_assignAllCountries();
            $this->_assignAllUserGroups();
            $this->_assignAllShippingMethods();
            $this->_disableDefaultDuplicateMethods();
        } else {
            // Just activate payment method
            $this->_togglePaymentMethod(true);
        }

        /**
         * This method is idempotent.
         * Only ONE webhook will be created.
         * NOTE for OXID EE:
         * This webhook will be shared by all subshops.
         * It must NOT be deleted on module deactivation.
         */
        $this->_registerPaypalWebhook();

        $this->clearTmp();
        $this->updateViews();

        /**
          * As the number of webhooks per app is limited to 10, there will be only ONE webhook for all shops.
          * Do not delete the webhook on module deactivation, as modules can be deactivated/activated on a per shop basis.
          * If you want or need to get rid of a webhook, delete it in the PayPal Dashboard.
          *
          */
    }

    /**
     * Actions sequence for module deactivation event.
     */
    public function deactivate()
    {
        $this->_togglePaymentMethod(false);
        $this->clearTmp();
        $this->updateViews();
    }

    /**
     * Clean cache folder content.
     *
     * @param string $sClearFolderPath Sub-folder path to delete from. Should be a full, valid path inside temp folder.
     *
     * @return boolean
     */
    public function clearTmp($sClearFolderPath = '')
    {
        $sFolderPath = $this->_getFolderToClear($sClearFolderPath);
        $hDirHandler = opendir($sFolderPath);

        if (!empty($hDirHandler)) {
            while (false !== ($sFileName = readdir($hDirHandler))) {
                $sFilePath = $sFolderPath . DIRECTORY_SEPARATOR . $sFileName;
                $this->_clear($sFileName, $sFilePath);
            }

            closedir($hDirHandler);
        }

        return true;
    }

    /**
     * Update database views.
     */
    public function updateViews()
    {
        /** @var oxDbMetaDataHandler $oDbHandler */
        $oDbHandler = $this->getNew('oxDbMetaDataHandler');
        $oDbHandler->updateViews();
    }


    /**
     * Get an ID of PayPal Plus payment method.
     *
     * @return string
     */
    protected function _getPayPalPlusMethodId()
    {
        if (is_null($this->_sPayPalPlusMethodId)) {
            $this->_sPayPalPlusMethodId = (string) $this->getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId();
        }

        return $this->_sPayPalPlusMethodId;
    }

    /**
     * Check if module was already installed.
     * As a criteria PayPal Plus payment method availability is checked.
     *
     * @return bool
     */
    protected function _isInstalled()
    {
        /** @var oxPayment $oPayment */
        $oPayment = $this->getNew('oxPayment');

        return (bool) $oPayment->load($this->_getPayPalPlusMethodId());
    }

    /**
     * Create PayPal Plus payment method in the eShop database.
     */
    protected function _createPaymentMethod()
    {
        /** @var oxPayment $oPayment */
        $oPayment = $this->getNew('oxPayment');

        if (!$oPayment->load($this->_getPayPalPlusMethodId())) {
            $oPayment->setId($this->_getPayPalPlusMethodId());
            $oPayment->oxpayments__oxactive = new oxField(1);
            $oPayment->oxpayments__oxaddsum = new oxField(0);
            $oPayment->oxpayments__oxaddsumtype = new oxField('abs');
            $oPayment->oxpayments__oxfromboni = new oxField(0);
            $oPayment->oxpayments__oxfromamount = new oxField(0);
            $oPayment->oxpayments__oxtoamount = new oxField(10000);
            $oPayment->oxpayments__oxsort = new oxField(-999); // Make the method topmost

            /** @var oxLang $oLanguage */
            $oLanguage = $this->getFromRegistry('oxLang');
            $aLanguages = $oLanguage->getLanguageIds();

            foreach ($aLanguages as $iLanguageId => $sAbbreviation) {
                $oPayment->setLanguage($iLanguageId);
                $oPayment->oxpayments__oxdesc = new oxField('PayPal Plus');
                $oPayment->oxpayments__oxlongdesc = new oxField('');
                $oPayment->save();
            }
        }
    }

    /**
     * Activate/Disable PayPal Plus payment method.
     *
     * @param bool $blIsActive
     */
    protected function _togglePaymentMethod($blIsActive)
    {
        /** @var oxPayment $oPayment */
        $oPayment = $this->getNew('oxPayment');

        if ($oPayment->load($this->_getPayPalPlusMethodId())) {
            $oPayment->oxpayments__oxactive = new oxField((int) (bool) $blIsActive);
            $oPayment->save();
        }
    }

    /**
     * Set PayPal Plus for all countries.
     * Selection is only those countries where PayPal Plus is available.
     */
    protected function _assignAllCountries()
    {
        $oConfig = $this->getShop()->getPayPalPlusConfig();
        $sAvailableCounties = $this->_arrayToInClauseValue((array) $oConfig->getPayPalPlusCounties());

        /** @var oxList $oCountries */
        $oCountries = $this->getNew('oxList');
        $oCountries->init('oxCountry');
        $oCountries->selectString(
            sprintf(
                "SELECT * FROM `%s` WHERE `OXACTIVE` = 1%s",
                getViewName('oxcountry'),
                (empty($sAvailableCounties) ? "" : " AND `OXISOALPHA2` IN ($sAvailableCounties)")
            )
        );

        $this->_assignList(
            $oCountries,
            'oxobject2payment',
            array('oxpaymentid' => $this->_getPayPalPlusMethodId(), 'oxobjectid' => '$', 'oxtype' => 'oxcountry')
        );
    }

    /**
     * Set PayPal Plus available for all user groups.
     */
    protected function _assignAllUserGroups()
    {
        /** @var oxList $oGroups */
        $oGroups = $this->getNew('oxList');
        $oGroups->init('oxgroups');
        $oGroups->selectString(sprintf("SELECT * FROM `%s` WHERE 1", getViewName('oxgroups')));

        $this->_assignList(
            $oGroups,
            'oxobject2group',
            array('oxobjectid' => $this->_getPayPalPlusMethodId(), 'oxgroupsid' => '$'),
            'oxObject2Group'
        );
    }

    /**
     * Set PayPal Plus available for all shipping methods.
     */
    protected function _assignAllShippingMethods()
    {
        /** @var oxDeliverySetList $oShippingMethods */
        $oShippingMethods = $this->getNew('oxDeliverySetList');
        $oShippingMethods->selectString(sprintf("SELECT * FROM `%s` WHERE 1", getViewName('oxdeliveryset')));

        $this->_assignList(
            $oShippingMethods,
            'oxobject2payment',
            array('oxpaymentid' => $this->_getPayPalPlusMethodId(), 'oxobjectid' => '$', 'oxtype' => 'oxdelset')
        );
    }

    /**
     * Creates database tables used by PayPal Plus module.
     */
    protected function _createDatabaseTables()
    {
        $oDb = $this->getShop()->getDb();

        $oDb->execute(
            "CREATE TABLE IF NOT EXISTS `payppaypalpluspayment` (
                `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Payment oxid id',
                `OXORDERID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Order id',
                `OXSALEID` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment sale id',
                `OXPAYMENTID` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment id',
                `OXSTATUS` varchar(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment status',
                `OXDATECREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Payment creation date',
                `OXTOTAL` double NOT NULL DEFAULT '0' COMMENT 'Total payment amount',
                `OXCURRENCY` varchar(32) NOT NULL DEFAULT '' COMMENT 'Payment currency',
                `OXPAYMENTOBJECT` BLOB NOT NULL DEFAULT '' COMMENT 'Serialized payment object',
                PRIMARY KEY (`OXID`),
                UNIQUE `OXORDERID` (`OXORDERID`),
                UNIQUE `OXSALEID` (`OXSALEID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PayPal Plus payment data model';"
        );

        $oDb->execute(
            "CREATE TABLE IF NOT EXISTS `payppaypalplusrefund` (
                `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Payment oxid id',
                `OXSALEID` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment sale id',
                `OXREFUNDID` varchar(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus refund id',
                `OXSTATUS` varchar(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus refund status',
                `OXDATECREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Refund creation date',
                `OXTOTAL` double NOT NULL DEFAULT '0' COMMENT 'Total refund amount',
                `OXCURRENCY` varchar(32) NOT NULL DEFAULT '' COMMENT 'Refund currency',
                `OXREFUNDOBJECT` BLOB NOT NULL DEFAULT '' COMMENT 'Serialized refund object',
                PRIMARY KEY (`OXID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PayPal Plus refund data model';"
        );

         $oDb->execute(
            "CREATE TABLE IF NOT EXISTS `payppaypalpluspui` (
                `OXID` CHAR(32) NOT NULL COMMENT 'Payment oxid id' COLLATE 'latin1_general_ci',
                `OXPAYMENTID` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment id' COLLATE 'latin1_general_ci',
                `OXREFERENCENUMBER` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI reference_number',
                `OXBANKNAME` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction bank name',
                `OXACCOUNTHOLDER` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction account holder',
                `OXIBAN` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction IBAN',
                `OXBIC` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction BIC',
                `OXDUEDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'PayPal Plus PuI due date',
                `OXTOTAL` DOUBLE NOT NULL DEFAULT '0' COMMENT 'PayPal Plus PuI Total invoice amount',
                `OXCURRENCY` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI invoice currency',
                `OXPUIOBJECT` TEXT NOT NULL COMMENT 'JSON representation of the payment instructions',
                PRIMARY KEY (`OXID`)
            )
            COMMENT='PayPal Plus Pay upon Invoice data model'
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB"
        );
    }

    /**
     * Disable default OXID eShop methods, which are covered by PayPal Plus methods.
     */
    protected function _disableDefaultDuplicateMethods()
    {
        $aDuplicateMethods = (array) $this->getShop()->getPayPalPlusConfig()->getExternalMethodsExceptions(false);

        foreach ($aDuplicateMethods as $sPaymentMethodId) {
            /** @var oxPayment $oPayment */
            $oPayment = $this->getNew('oxPayment');

            if ($oPayment->load($sPaymentMethodId)) {
                $oPayment->oxpayments__oxactive = new oxField(0);
                $oPayment->save();
            }
        }
    }

    /**
     * Check if provided path is inside eShop tpm/ folder or use the tmp/ folder path.
     *
     * @param string $sClearFolderPath
     *
     * @return string
     */
    protected function _getFolderToClear($sClearFolderPath = '')
    {
        $sTempFolderPath = (string) $this->getShop()->getSetting('sCompileDir');

        if (!empty($sClearFolderPath) and (strpos($sClearFolderPath, $sTempFolderPath) !== false)) {
            $sFolderPath = $sClearFolderPath;
        } else {
            $sFolderPath = $sTempFolderPath;
        }

        return $sFolderPath;
    }

    /**
     * Check if resource could be deleted and
     * delete it if it's a file or call recursive folder deletion if it is a directory.
     *
     * @param string $sFileName
     * @param string $sFilePath
     */
    protected function _clear($sFileName, $sFilePath)
    {
        if (!in_array($sFileName, array('.', '..', '.htaccess'))) {
            if (is_file($sFilePath)) {
                @unlink($sFilePath);
            } else {
                $this->clearTmp($sFilePath);
            }
        }
    }

    /**
     * Assign a list to an object.
     *
     * @param oxList $oList
     * @param string $sRelationTable
     * @param array  $aFieldsMap Assoc array with key as field name. If value is "$" it is replaces with list item ID.
     * @param string $sRelationClass
     */
    protected function _assignList(oxList $oList, $sRelationTable, array $aFieldsMap, $sRelationClass = 'oxBase')
    {
        if ($oList->count()) {
            foreach ($oList as $oListItem) {
                $this->_assignRelation($oListItem, $sRelationClass, $sRelationTable, $aFieldsMap);
            }
        }
    }

    /**
     * Assign a list item object to a target object.
     *
     * @param object $oListItem
     * @param string $sRelationClass
     * @param string $sRelationTable
     * @param array  $aFieldsMap
     */
    protected function _assignRelation($oListItem, $sRelationClass, $sRelationTable, array $aFieldsMap)
    {
        /** @var oxBase $oRelation */
        $oRelation = $this->getNew($sRelationClass);

        if ($sRelationClass === 'oxBase') {
            $oRelation->init($sRelationTable);
        }

        foreach ($aFieldsMap as $sField => $sValue) {
            $sFieldName = $sRelationTable . '__' . $sField;
            $oRelation->$sFieldName = new oxField($sValue == '$' ? $oListItem->getId() : $sValue);
        }

        $oRelation->save();
    }

    /**
     * Convert an array to a list of values as string to use in MySQL "IN" clause.
     *
     * @param array $aValues
     *
     * @return string
     */
    protected function _arrayToInClauseValue(array $aValues)
    {
        $oDb = $this->getShop()->getDb();
        $aCleanValues = array();

        foreach ($aValues as $mValue) {
            $aCleanValues[] = $oDb->quote(trim((string) $mValue));
        }

        return (string) implode(', ', $aCleanValues);
    }

    /**
     * Register a PayPal webhook for this App.
     * This method is idempotent, so a webhook will only be created if there is no webhook registered already for this App.
     *
     */
    protected function _registerPaypalWebhook()
    {
        $blCreateWebhook = false;
        //if the Api Settings are empty, web hooks wont work
        $this->_checkApiSettings();

        $oWebHookList = $this->_getWebHookList();
        if (! $oWebHookList->getWebhooks() ) {
            $blCreateWebhook = true;
        } elseif ( ! $this->_webHookListContainsShopWebhook($oWebHookList)) {
            $blCreateWebhook = true;
        }

        if ($blCreateWebhook) {
            $this->_createPaypalWebhook();
        }
    }

    /**
     * Get list of all registered PayPal webhooks
     *
     * @codeCoverageIgnore
     *
     * @return \PayPal\Api\WebhookList
     */
    protected function _getWebHookList()
    {
        /** @var paypPayPalPlusSdk $oSdk */
        $oSdk = $this->getSdk();

        $oApiContext = $this->_getPayPalApiContext();

        /** @var PayPal\Api\Webhook $oWebHook */
        $oWebHook = $oSdk->newWebhook();

        /** @var PayPal\Api\WebhookList $oWebhookList */
        $oWebhookList = $oWebHook::getAll($oApiContext);

        return $oWebhookList;
    }

    protected function _webHookListContainsShopWebhook($oWebHookList) {
        $blReturn = false;

        /** @var \PayPal\Api\Webhook $oWebhook */
        foreach ($oWebHookList->getWebhooks() as $oWebhook ) {
            if (false !== strpos($oWebhook->getUrl(), $this->_getWebhookUrl() ) ) {
                $blReturn = true;
                break;
            }
        }

        return $blReturn;
    }

    /**
     * @throws oxException
     */
    protected function _createPaypalWebhook()
    {
        /** @var paypPayPalPlusSdk $oSdk */
        $oSdk = $this->getSdk();
        $oApiContext = $this->_getPayPalApiContext();

        $sWebhookUrl = $this->_getWebhookUrl();

        /** @var PayPal\Api\Webhook $oWebHook */
        $oWebHook = $oSdk->newWebhook();
        $oWebHook->setUrl($sWebhookUrl);

        /** Subcribe to evnet types */
        $webhookEventTypes = $this->_getSubscribedEventTypes();
        $oWebHook->setEventTypes($webhookEventTypes);

        /** Register the webhook on PayPal */
        try {
            $oWebHook->create($oApiContext);
        } catch (Exception $e) {
            /** @var oxException $oException */
            $oException = oxNew('oxException');
            $oException->setMessage($e->getMessage());
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oException);
        }
    }

    /**
     * @return string
     * @throws oxException
     */
    protected function _getWebhookUrl()
    {
        $sSslShopUrl = $this->_getShopSslUrl();

        $sWebhookUrl = trim($sSslShopUrl, '/') . '/index.php?cl=payppaypalpluswebhook';

        return $sWebhookUrl;
    }

    /**
     * Checks if API credentials are entered before working with webhooks
     *
     * @throws oxException
     */
    protected function _checkApiSettings()
    {
        $oPayPalConfig = $this->getShop()->getPayPalPlusConfig();
        $sClientId = $oPayPalConfig->getClientId();
        $sSecret = $oPayPalConfig->getSecret();

        if (empty($sClientId) || empty($sSecret)) {
            $sMessage = $this->getShop()->translate('payp_PAYPALPLUS_ERROR_NO_USER_CREDENTIALS');
            /** @var oxException $oException */
            $oException = oxNew('oxException');
            $oException->setMessage($sMessage);
            throw $oException;
        }
    }

    protected function _isSslShopUrl($sUrl) {
        return false !== strpos($sUrl, 'https://');
    }

    /**
     * @return object|paypPayPalPlusSdk
     */
    protected function _getPayPalPlusSuperCfg()
    {
        if (is_null($this->_oPayPalPlusSuperCfg)) {
            /** @var paypPayPalPlusSuperCfg $oPayPalPlusSuperCfg */
            $this->_oPayPalPlusSuperCfg = oxNew('paypPayPalPlusSuperCfg');
        }

        return $this->_oPayPalPlusSuperCfg;
    }

    /**
     * Get
     *
     * @return null|\PayPal\Rest\ApiContext
     */
    protected function _getPayPalApiContext()
    {
        if (is_null($this->_oPayPalApiContext)) {
            /** @var \PayPal\Rest\ApiContext  _oPayPalApiContext */
            $this->_oPayPalApiContext = $this->getShop()->getPayPalPlusSession()->getApiContext();
        }

        return $this->_oPayPalApiContext;
    }

    /**
     * Get EventTypes the Webhook should be subscribed
     *
     * @param $oSdk
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    protected function _getSubscribedEventTypes()
    {
        $webhookEventTypes = array();
        $oSdk = $this->getSdk();

        $aSubscribedEventTypes = $this->getShop()->getPayPalPlusConfig()->getSubscribedEventTypes();
        foreach ($aSubscribedEventTypes as $eventName) {
            $oEventType = $oSdk->newWebhookEventType();
            $oEventType->setName($eventName);
            $webhookEventTypes[] = $oEventType;
        }

        return $webhookEventTypes;
    }

    /**
     * @return mixed
     * @throws oxException
     */
    protected function _getShopSslUrl()
    {
        /**
         * The URL the webhook is bound to
         * This MUST be a SSL URL
         */
        $blIsSslShopUrlAvailable = true;
        $sSslShopUrl = $this->getConfig()->getConfigParam('sSSLShopURL');
        if (!$this->_isSslShopUrl($sSslShopUrl)) {
            $blIsSslShopUrlAvailable = false;
        }
        /** Fallback to normal shop url and see if it is SSL */
        if (!$blIsSslShopUrlAvailable) {
            $sSslShopUrl = $this->getConfig()->getConfigParam('sShopURL');
            if ($this->_isSslShopUrl($sSslShopUrl)) {
                $blIsSslShopUrlAvailable = true;
            }
        }
        if (!$blIsSslShopUrlAvailable) {
            $sMessage = $this->getShop()->translate('PAYP_PAYPALPLUS_ERROR_NOSSL_URL');
            /** @var oxException $oException */
            $oException = oxNew('oxException');
            $oException->setMessage($sMessage);
            throw $oException;
        }

        return $sSslShopUrl;
    }
}