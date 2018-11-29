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
 * Class paypPayPalPlusTaxationHandler.
 * Transforms PayPal models with tax and price fields according with shop price mode.
 */
class paypPayPalPlusTaxationHandler extends paypPayPalPlusSuperCfg
{

    /**
     * Is shop operating in B2C mode (price shown including tax).
     *
     * @var null|bool
     */
    protected $_blIsB2cShop = null;

    /**
     * Data converter instance.
     *
     * @var null|paypPayPalPlusDataConverter
     */
    protected $_oConverter = null;


    /**
     * Check if shop is in B2C mode.
     * In B2C mode prices are shown including tax values.
     *
     * @return bool
     */
    public function isB2cShop()
    {
        if (is_null($this->_blIsB2cShop)) {
            $this->_blIsB2cShop = !((bool) $this->getShop()->getConfig()->getConfigParam('blShowNetPrice'));
        }

        return $this->_blIsB2cShop;
    }

    /**
     * Get data converter instance.
     *
     * @return paypPayPalPlusDataConverter
     */
    public function getConverter()
    {
        if (is_null($this->_oConverter)) {
            $this->_oConverter = $this->getShop()->getConverter();
        }

        return $this->_oConverter;
    }


    /**
     * If shop is in B2C mode, adjust model price and tax fields:
     *  - Add tax value to price
     *  - Set tax value as zero (will be ignored in PayPal)
     * In case of B2B shop, models remain unchanged.
     *
     * @param PayPal\Api\Item|PayPal\Api\Details $oModel
     * @param string                             $sPriceField
     *
     * @return PayPal\Api\Item|PayPal\Api\Details
     */
    public function adjustedTaxation(PayPal\Common\PayPalModel $oModel, $sPriceField = 'Price')
    {
        if ($this->isB2cShop() and $this->_modelHasPriceField($oModel, $sPriceField)) {
            $sGetterName = 'get' . $sPriceField;
            $sSetterName = 'set' . $sPriceField;

            $sPrice = $this->getConverter()->price((double) $oModel->$sGetterName() + (double) $oModel->getTax());
            $oModel->$sSetterName($sPrice);

            $oModel->setTax($this->getConverter()->price(0.0));
        }

        return $oModel;
    }


    /**
     * Check if PayPal model object has tax and price fields.
     *
     * @param \PayPal\Common\PayPalModel $oModel
     * @param string                     $sPriceField
     *
     * @return bool
     */
    protected function _modelHasPriceField(PayPal\Common\PayPalModel $oModel, $sPriceField)
    {
        return method_exists($oModel, 'getTax') and method_exists($oModel, ('get' . $sPriceField));
    }
}
