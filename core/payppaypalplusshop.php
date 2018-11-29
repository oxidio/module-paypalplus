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
 * Class paypPayPalPlusShop
 * OXID eShop wrapper class for shop methods aliases.
 * It also provides getters for most common module core classes from registry.
 */
class paypPayPalPlusShop extends oxSuperCfg
{

    /**
     * Get an instance of itself.
     *
     * @return paypPayPalPlusShop
     */
    public static function getShop()
    {
        return oxRegistry::get(__CLASS__);
    }

    /**
     * Get request parameter value.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getRequestParameter($sKey)
    {
        /** @var oxConfig $oConfig */
        $oConfig = $this->getConfig();

        return $oConfig->getRequestParameter($sKey);
    }

    /**
     * Get OXID eShop configuration parameter value.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getSetting($sKey)
    {
        /** @var oxConfig $oConfig */
        $oConfig = $this->getConfig();

        return $oConfig->getConfigParam($sKey);
    }

    /**
     * Set OXID eShop session variable.
     *
     * @param string $sKey
     * @param mixed  $mValue
     */
    public function setSessionVariable($sKey, $mValue)
    {
        oxRegistry::getSession()->setVariable($sKey, $mValue);
    }

    /**
     * Get OXID eShop session variable.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getSessionVariable($sKey)
    {
        return oxRegistry::getSession()->getVariable($sKey);
    }

    /**
     * Delete OXID eShop session variable.
     *
     * @param string $sKey
     */
    public function deleteSessionVariable($sKey)
    {
        oxRegistry::getSession()->deleteVariable($sKey);
    }

    /**
     * Get a value of request parameter by key and if it is empty,
     * try to load session parameter value by the same key.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getRequestOrSessionParameter($sKey)
    {
        $mValue = $this->getRequestParameter($sKey);

        if (empty($mValue)) {
            $mValue = $this->getSessionVariable($sKey);
        }

        return $mValue;
    }

    /**
     * Set shop basket to session.
     *
     * @return paypPayPalPlusOxBasket|oxBasket
     */
    public function setBasket(oxBasket $oBasket)
    {
        oxRegistry::getSession()->setBasket($oBasket);
    }

    /**
     * Get shop basket from session.
     *
     * @return paypPayPalPlusOxBasket|oxBasket
     */
    public function getBasket()
    {
        return oxRegistry::getSession()->getBasket();
    }

    /**
     * Get current shop user.
     *
     * @return oxUser
     */
    public function getUser()
    {
        $oUser = parent::getUser();

        if (!($oUser instanceof oxUser)) {
            $oUser = $this->getNew('oxUser');
        }

        return $oUser;
    }

    /**
     * An alias for OXID eShop oxNew factory.
     *
     * @param $sClassName
     *
     * @return object
     */
    public function getNew($sClassName)
    {
        return oxNew($sClassName);
    }

    /**
     * An alias for OXID eShop oxRegistry objects getter.
     *
     * @param $sClassName
     *
     * @return object
     */
    public function getFromRegistry($sClassName)
    {
        return oxRegistry::get($sClassName);
    }

    /**
     * Get OXID eShop database connector.
     *
     * @return oxLegacyDb
     */
    public function getDb()
    {
        return oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
    }

    /**
     * Get OXID eShop string helper.
     *
     * @return oxStrRegular|oxStrMb
     */
    public function getStr()
    {
        return oxStr::getStr();
    }

    /**
     * Get OXID eShop config instance.
     *
     * @return oxConfig
     */
    public function getConfig()
    {
        return parent::getConfig();
    }

    /**
     * Get OXID eShop utils instance.
     *
     * @return oxUtils
     */
    public function getUtils()
    {
        return oxRegistry::getUtils();
    }

    /**
     * Get OXID eShop oxLang instance.
     *
     * @return oxLang
     */
    public function getLang()
    {
        return oxRegistry::getLang();
    }

    /**
     * Translate language code.
     *
     * @param string $sCode
     * @param bool   $blAdminMode
     *
     * @return string
     */
    public function translate($sCode, $blAdminMode = false)
    {
        return oxRegistry::getLang()->translateString($sCode, oxRegistry::getLang()->getTplLanguage(), $blAdminMode);
    }


    /* ---------------------------- *
     * Recently used module classes *
     * ---------------------------- */

    /**
     * Get loaded PayPal Plus module data class.
     *
     * @return paypPayPalPlusModule
     */
    public function getPayPalPlusModule()
    {
        return $this->getFromRegistry('paypPayPalPlusModule');
    }

    /**
     * Get PayPal Plus configuration instance.
     *
     * @return paypPayPalPlusConfig
     */
    public function getPayPalPlusConfig()
    {
        return $this->getFromRegistry('paypPayPalPlusConfig');
    }

    /**
     * Get initialized PayPal Plus session instance.
     *
     * @return paypPayPalPlusSession
     */
    public function getPayPalPlusSession()
    {
        /** @var paypPayPalPlusSession $oPayPalSession */
        $oPayPalSession = $this->getFromRegistry('paypPayPalPlusSession');
        $oPayPalSession->init();

        return $oPayPalSession;
    }

    /**
     * Get data access helper instance.
     *
     * @return paypPayPalPlusDataAccess
     */
    public function getDataAccess()
    {
        return $this->getFromRegistry('paypPayPalPlusDataAccess');
    }

    /**
     * Get data casting and formatting helper instance.
     *
     * @return paypPayPalPlusDataConverter
     */
    public function getConverter()
    {
        return $this->getFromRegistry('paypPayPalPlusDataConverter');
    }

    /**
     * Get module, payment method and session data validator instance.
     *
     * @return paypPayPalPlusValidator
     */
    public function getValidator()
    {
        return $this->getFromRegistry('paypPayPalPlusValidator');
    }

    /**
     * Get error handler instance.
     *
     * @return paypPayPalPlusErrorHandler
     */
    public function getErrorHandler()
    {
        return $this->getFromRegistry('paypPayPalPlusErrorHandler');
    }
}
