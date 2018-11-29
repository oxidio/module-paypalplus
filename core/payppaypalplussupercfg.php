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
 * Class paypPayPalPlusSuperCfg.
 * A wrapper of oxSuperCfg for PayPal Plus module.
 * In addition to oxSuperCfg, it provides SDK, Config and Shop instances, aliases for new and registry objects fetching.
 */
class paypPayPalPlusSuperCfg extends oxSuperCfg
{

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * PayPal REST API SDK driver instance.
     *
     * @var null|paypPayPalPlusSdk
     */
    protected $_oSdk = null;


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
     * Get PayPal REST API SDK wrapper.
     *
     * @return paypPayPalPlusSdk
     */
    public function getSdk()
    {
        if (is_null($this->_oSdk)) {
            $this->_oSdk = $this->getFromRegistry('paypPayPalPlusSdk');
        }

        return $this->_oSdk;
    }


    /**
     * Crate a new class object.
     *
     * @param $sClassName
     *
     * @return object
     */
    public function getNew($sClassName)
    {
        return $this->getShop()->getNew($sClassName);
    }

    /**
     * Get object form registry.
     *
     * @param $sClassName
     *
     * @return object
     */
    public function getFromRegistry($sClassName)
    {
        return $this->getShop()->getFromRegistry($sClassName);
    }


    /**
     * Check if method name has an expected prefix and return what's after the prefix.
     * This method is used to parse magic calls.
     *
     * Examples:
     *   $this->parseCallFor('get', 'justSomeMethod')   -> return null
     *   $this->parseCallFor('get', 'getField')         -> return 'Field'
     *   $this->parseCallFor('new', 'newObject')        -> return 'Object'
     *   ...
     *
     * @param string $sExpectedPrefix
     * @param string $sMethodName
     *
     * @return null|string Null on mismatch or method name part with no prefix.
     */
    public function parseCallFor($sExpectedPrefix, $sMethodName)
    {
        $oStr = $this->getShop()->getStr();

        $sPrefixLength = (int) $oStr->strlen($sExpectedPrefix);
        $sMethodLength = (int) $oStr->strlen($sMethodName);

        if ($sPrefixLength == 0 or ($sPrefixLength >= $sMethodLength)) {
            return null;
        }

        $sMethodPrefix = $oStr->substr($sMethodName, 0, $sPrefixLength);

        if ($sMethodPrefix !== $sExpectedPrefix) {
            return null;
        }

        return $oStr->substr($sMethodName, $sPrefixLength, ($sMethodLength - $sPrefixLength));
    }
}
