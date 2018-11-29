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
 * @link          http://www.oxid-esales.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2016
 */
class admin_payppaypalplusmodule_config extends admin_payppaypalplusmodule_config_parent
{
    /**
     * Module ID
     *
     * @var string
     */
    const MODULE_ID = 'payppaypalplus';

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * OLD experience settings
     *
     * @var array $_aOldValues
     */
    protected $_aOldValues = array();

    /**
     * New experience settings
     *
     * @var array $_aNewValues
     */
    protected $_aNewValues = array();

    /**
     * Module settings prefix
     *
     * @var string $_moduleSettingsPrefix
     */
    protected $_moduleSettingsPrefix = null;

    /**
     * Define setting keys as constants
     */
    const profileIdKey = 'ExpProfileId';
    const nameKey = 'ExpName';
    const brandKey = 'ExpBrand';
    const logoKey = 'ExpLogo';
    const localeKey = 'ExpLocale';

    /**
     * Saves Paypal Experience setting, if needed tries to retrieve Paypal Payment Experience Profile ID
     */
    public function saveConfVars()
    {
        /** If we are saving not our module variables don't do anything else */
        if ($this->getEditObjectId() != self::MODULE_ID) {
            return $this->_returnParent();
        }

        /** Set new and old values */
        $this->_init();

        /** If OLD values are equal to NEW ones (nothing was changed), or profile id and all info was removed at all */
        if ($this->_aNewValues == $this->_aOldValues || !array_filter($this->_aNewValues)) {
            return $this->_returnParent();
        }

        if ($this->_retrieveProfile()===true) {
            return $this->_returnParent();
        }

        if ($this->_updateProfile()===true) {
            return $this->_returnParent();
        }

        if ($this->_createProfile()===true) {
            return $this->_returnParent();
        }

        return $this->_returnParent();
    }

    /**
     * If we have not empty profile ID but we don't have another info try to get Profile details by ProfileId
     */
    protected function _retrieveProfile()
    {
        $aNewValuesWithoutProfileId = $this->_aNewValues;
        unset($aNewValuesWithoutProfileId[self::profileIdKey]);

        if (!empty($this->_aNewValues[self::profileIdKey]) && !array_filter($aNewValuesWithoutProfileId)) {
            $result = $this->_doRequest('get', $this->_aNewValues[self::profileIdKey]);
            if ($result !== false) {
                /** @var \Paypal\Api\WebProfile $result*/
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::nameKey] = $result->getName();
                if ($result->getPresentation()) {
                    $_POST['confstrs'][$this->_moduleSettingsPrefix . self::brandKey] = $result->getPresentation()->getBrandName();
                    $_POST['confstrs'][$this->_moduleSettingsPrefix . self::logoKey] = $result->getPresentation()->getLogoImage();
                    $_POST['confstrs'][$this->_moduleSettingsPrefix . self::localeKey] = $result->getPresentation()->getLocaleCode();
                }
            } else {
                /** Don't save and use the profile key which does not exists */
                unset($_POST['confstrs'][$this->_moduleSettingsPrefix . self::profileIdKey]);
            }
            return true;
        }

        return false;
    }

    /**
     * If we have not empty profile ID attempt to update the data
     *
     * @return boolean
     */
    protected function _updateProfile()
    {
        if (!empty($this->_aNewValues[self::profileIdKey])) {

            if ($this->_validateValues() === false) {
                return true;
            }

            if ($this->_doRequest('update', $this->_aNewValues) == true) {
                return true;
            } else {
                /** If we were not able to save new values due some error - revert back and save old values */
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::profileIdKey] = $this->_aOldValues[self::profileIdKey];
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::nameKey] = $this->_aOldValues[self::nameKey];
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::brandKey] = $this->_aOldValues[self::brandKey];
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::logoKey] = $this->_aOldValues[self::logoKey];
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::localeKey] = $this->_aOldValues[self::localeKey];
                return true;
            }
        }

        return false;
    }

    /**
     * If we have empty profile ID attempt to create the new one
     *
     * @return boolean
     */
    protected function _createProfile()
    {
        if (empty($this->_aNewValues[self::profileIdKey])) {

            if ($this->_validateValues() === false) {
                return true;
            }

            $result = $this->_doRequest('create', $this->_aNewValues);
            if ($result !== false) {
                /** SET new profile ID for saving */
                $_POST['confstrs'][$this->_moduleSettingsPrefix . self::profileIdKey] = $result->getId();
                return true;
            }
        }

        return false;
    }

    /**
     * Method Set's new and old Experience Profile values
     *
     * @return bool
     */
    protected function _init()
    {
        /** @var paypPayPalPlusShop $oShop */
        $oShop = $this->_getShop();
        $aConfVars = $oShop->getConfig()->getRequestParameter('confstrs');
        $this->_moduleSettingsPrefix = $oShop->getPayPalPlusConfig()->getModuleSettingsPrefix();

        $this->_aOldValues = array(
            self::profileIdKey => $oShop->getPayPalPlusConfig()->getModuleSetting(self::profileIdKey),
            self::nameKey => $oShop->getPayPalPlusConfig()->getModuleSetting(self::nameKey),
            self::brandKey => $oShop->getPayPalPlusConfig()->getModuleSetting(self::brandKey),
            self::logoKey => $oShop->getPayPalPlusConfig()->getModuleSetting(self::logoKey),
            self::localeKey => $oShop->getPayPalPlusConfig()->getModuleSetting(self::localeKey),
        );

        $this->_aNewValues = array(
            self::profileIdKey => $aConfVars[$this->_moduleSettingsPrefix . self::profileIdKey],
            self::nameKey => $aConfVars[$this->_moduleSettingsPrefix . self::nameKey],
            self::brandKey => $aConfVars[$this->_moduleSettingsPrefix . self::brandKey],
            self::logoKey => $aConfVars[$this->_moduleSettingsPrefix . self::logoKey],
            self::localeKey => $aConfVars[$this->_moduleSettingsPrefix . self::localeKey],
        );
    }

    /**
     * Returns parent method call
     *
     * @codeCoverageIgnore External call to Paypal
     */
    protected function _returnParent(){
        return parent::saveConfVars();
    }

    /**
     * Get OXID eShop wrapper.
     *
     * @return paypPayPalPlusShop
     */
    protected function _getShop()
    {
        if (is_null($this->_oShop)) {
            $this->_oShop = paypPayPalPlusShop::getShop();
        }

        return $this->_oShop;
    }

    /**
     * Validate required values
     *
     * @return bool
     */
    protected function _validateValues(){
        if (empty($this->_aNewValues[self::nameKey])) {
            $error = new \stdClass();
            $error->message = $this->_getShop()->translate('PAYP_PAYPALPLUS_ERR_EXP_NAME_EMPTY', true);
            $this->_setErrorMessage($error);
            return false;
        }

        return true;
    }

    /**
     * Method makes request to specified method with specify Params
     *
     * @codeCoverageIgnore External call to Paypal
     *
     * @param $method
     * @param $params
     * @return bool|\PayPal\Api\WebProfile
     */
    protected function _doRequest($method, $params)
    {
        /** @var paypPayPalPlusWebProfileHandler $oWebProfileHandler */
        $oWebProfileHandler = $this->_getShop()->getFromRegistry('paypPayPalPlusWebProfileHandler');

        try {
            return $oWebProfileHandler->{$method}($this->_getShop()->getPayPalPlusSession()->getApiContext(), $params);
        } catch (\PayPal\Exception\PayPalConnectionException $ex) {
            $this->_setErrorMessage(json_decode($ex->getData()));
            return false;
        }
    }

    /**
     * Method set's error message to template
     *
     * @param $error object
     */
    protected function _setErrorMessage($error) {
        $message = '<br/>' . $error->message;
        if (is_array($error->details) && count($error->details)>0){
            $message.=':';
            foreach ($error->details as $item) {
                $message .= '<br/>' . $item->issue;
            }
        }
        $this->_aViewData[self::MODULE_ID . 'error'] = $message;
    }
}