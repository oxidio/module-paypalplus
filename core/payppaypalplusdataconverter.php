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
 * Class paypPayPalPlusDataConverter.
 * Data casting and transformation for PayPal API.
 */
class paypPayPalPlusDataConverter extends paypPayPalPlusSuperCfg
{

    /**
     * Convert a value to string.
     * Optionally crop the string to a specified length.
     *
     * @param mixed $mValue
     * @param int   $iMaxLength
     *
     * @return string
     */
    public function string($mValue, $iMaxLength = 100)
    {
        $oStr = $this->getShop()->getStr();

        $sValue = $this->_toString($mValue);

        if ($oStr->strlen($sValue) > (int) $iMaxLength) {
            $sValue = $this->getShop()->getStr()->substr($sValue, 0, (int) $iMaxLength);
        }

        return $sValue;
    }

    /**
     * Convert a value to float number presented as string.
     *
     * @param mixed $mValue
     *
     * @return string
     */
    public function number($mValue)
    {
        return (string) $this->_toNumber($mValue);
    }

    /**
     * Convert a value to formatted price string.
     *
     * @param mixed $mValue
     *
     * @return string
     */
    public function price($mValue)
    {
        $oPayPalConfig = $this->getShop()->getPayPalPlusConfig();

        $dValue = $this->_toNumber($mValue);

        return (string) number_format(
            $dValue,
            2,
            $oPayPalConfig->getDecimalsSeparator(),
            $oPayPalConfig->getThousandsSeparator()
        );
    }

    /**
     * Convert a value to date and time string in ISO format "YYYY-MM-DD HH:ii:ss".
     * Returns empty string for an empty value.
     *
     * @todo (nice2have): Use DateTime::createFromFormat and then transform to ISO format.
     *
     * @param $mValue
     *
     * @return string
     */
    public function date($mValue)
    {
        $sDate = '';
        $sValue = $this->_toString($mValue);
        $iTimestamp = (int) strtotime($sValue);

        if ($iTimestamp > 0) {
            $sDate = $this->_toString(date('Y-m-d H:i:s', $iTimestamp));
        }

        return $sDate;
    }


    /**
     * Convert value to string.
     * Additionally trims the string.
     * In case OXID eShop is not in UTF-8 move, value is converted to the UTF-8.
     *
     * @param mixed $mValue
     *
     * @return string
     */
    protected function _toString($mValue)
    {
        if (!is_scalar($mValue)) {
            return '';
        }

        $sValue = trim((string) $mValue);

        if (!$this->getShop()->getConfig()->getConfigParam('iUtfMode')) {
            $sValue = iconv('ISO-8859-15', 'UTF-8', $sValue);
        }

        return $sValue;
    }

    /**
     * Convert value to float number.
     *
     * @param $mValue
     *
     * @return float
     */
    protected function _toNumber($mValue)
    {
        if (!is_scalar($mValue)) {
            return 0.0;
        }

        return (float) $mValue;
    }
}
