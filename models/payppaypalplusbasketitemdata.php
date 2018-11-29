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
 * Class paypPayPalPlusBasketItemData.
 * Basket item data provider for SDK model Item.
 */
class paypPayPalPlusBasketItemData extends paypPayPalPlusDataProvider
{

    /**
     * Fields names to use for SDK object Item.
     *
     * @var array
     */
    protected $_aFields = array('Name', 'Currency', 'Price', 'Quantity', 'Tax', 'Sku');

    /**
     * OXID eShop basket item instance to get data from.
     * It should be set before fetching the data.
     *
     * @var null|oxBasketItem
     */
    protected $_oBasketItem = null;


    /**
     * Set a basket item object to get data from.
     *
     * @param oxBasketItem $oBasketItem
     */
    public function setBasketItem(oxBasketItem $oBasketItem)
    {
        $this->_oBasketItem = $oBasketItem;
    }

    /**
     * Get a basket item object
     *
     * @return null|oxBasketItem
     */
    public function getBasketItem()
    {
        return $this->_oBasketItem;
    }


    /**
     * Get basket item data for SDK object Item.
     *
     * @return array
     */
    public function getData()
    {
        $oUtils = $this->getDataUtils();
        $oConvert = $this->getConverter();

        /** @var oxBasket $oBasket */
        $oBasket = $this->_getSourceObject('oxBasket');

        /** @var oxBasketItem $oItem */
        $oItem = $this->_getSourceObject('oxBasketItem');

        return array(
            'Name'     => $oConvert->string($oUtils->invokeGet($oItem, 'getArticle', 'oxarticles__oxtitle:$', 'value:$'), 127),
            'Currency' => $oConvert->string($oUtils->invokeGet($oBasket, 'getBasketCurrency', 'name:$'), 3),
            'Price'    => $oConvert->price($oUtils->invokeGet($oItem, 'getUnitPrice', 'getNettoPrice')),
            'Quantity' => $oConvert->number($oUtils->invokeGet($oItem, 'getAmount')),
            'Tax'      => $oConvert->price($oUtils->invokeGet($oItem, 'getUnitPrice', 'getVatValue')),
            'Sku'      => $oConvert->string($oUtils->invokeGet($oItem, 'getArticle', 'oxarticles__oxartnum:$', 'value:$'), 50),
        );
    }


    /**
     * Get current basket and set basket item objects.
     *
     * @return array
     */
    protected function _getSources()
    {
        return array(
            'oxBasket'     => $this->getShop()->getBasket(),
            'oxBasketItem' => $this->_oBasketItem,
        );
    }
}
