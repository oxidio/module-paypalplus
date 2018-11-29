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
 * Class paypPayPalPlusBasketData.
 * Basket data provider for SDK models: ItemList, Details and Amount.
 */
class paypPayPalPlusBasketData extends paypPayPalPlusDataProvider
{

    /**
     * Fields names to use for SDK objects Amount and Details.
     *
     * @var array
     */
    protected $_aFields = array(
        'Amount'   => array('Total', 'Currency'),
        'Details'  => array('Subtotal', 'Tax', 'HandlingFee', 'Insurance', 'Shipping', 'ShippingDiscount'),
        'ItemList' => array(),
    );


    /**
     * Get basket data mapped for SDK objects.
     *
     * @return array
     */
    public function getData()
    {
        $oUtils = $this->getDataUtils();
        $oConvert = $this->getConverter();
        $aShopData = $this->_getData();

        $dError = (double) $this->_calculatePayPalTaxError($aShopData);
        $dHandlingExtra = $dDiscountExtra = 0.0;

        if ($dError >= 0.0) {
            $dHandlingExtra = $dError;
        } else {
            $dDiscountExtra = $dError;
        }

        return array(
            'Amount'   => array(
                'Total'    => $oConvert->price($oUtils->getArrayValue('Total', $aShopData)),
                'Currency' => $oConvert->string($oUtils->getArrayValue('Currency', $aShopData), 3),
            ),
            'Details'  => array(
                'Subtotal'         => $oConvert->price($oUtils->getArrayValue('Subtotal', $aShopData)),
                'Tax'              => $oConvert->price($oUtils->getArrayValue('Tax', $aShopData)),
                'HandlingFee'      => $oConvert->price($oUtils->getArrayValue('HandlingFee', $aShopData) + $dHandlingExtra),
                'Insurance'        => $oConvert->price($oUtils->getArrayValue('Insurance', $aShopData)),
                'Shipping'         => $oConvert->price($oUtils->getArrayValue('Shipping', $aShopData)),
                'ShippingDiscount' => $oConvert->price($oUtils->getArrayValue('ShippingDiscount', $aShopData) + $dDiscountExtra),
            ),
            'ItemList' => (array) $oUtils->getArrayValue('ItemList', $aShopData),
        );
    }


    /**
     * Get eShop current basket object.
     *
     * @return array
     */
    protected function _getSources()
    {
        return array('oxBasket' => $this->getShop()->getBasket());
    }

    /**
     * Get basket data in shop format.
     * Price values are already calculated according to PayPal rules.
     *
     * @return array
     */
    protected function _getData()
    {
        $oUtils = $this->getDataUtils();

        /** @var oxBasket $oBasket */
        $oBasket = $this->_getSourceObject('oxBasket');

        $aShopData = array(
            'Total'            => (double) $oUtils->invokeGet($oBasket, 'getPrice', 'getPrice'),
            'Currency'         => (string) $oUtils->invokeGet($oBasket, 'getBasketCurrency', 'name:$'),
            'Subtotal'         => 0.0,
            'Tax'              => 0.0,
            'HandlingFee'      => ((double) $oUtils->invokeGet($oBasket, 'getCosts:oxpayment', 'getBruttoPrice') +
                                   (double) $oUtils->invokeGet($oBasket, 'getCosts:oxgiftcard', 'getBruttoPrice') +
                                   (double) $oUtils->invokeGet($oBasket, 'getCosts:oxwrapping', 'getBruttoPrice')),
            'Insurance'        => (double) $oUtils->invokeGet($oBasket, 'getCosts:oxtsprotection', 'getBruttoPrice'),
            'Shipping'         => (double) $oUtils->invokeGet($oBasket, 'getCosts:oxdelivery', 'getBruttoPrice'),
            'ShippingDiscount' => -1.0 * (double) $oUtils->invokeGet($oBasket, 'getTotalDiscountSum'),
            'ItemList'         => array(),
        );

        $oBasketItems = (array) $oUtils->invokeGet($oBasket, 'getContents');

        foreach ($oBasketItems as $oBasketItem) {
            /** @var oxBasketItem $oBasketItem */

            /** @var paypPayPalPlusBasketItemData $oBasketItemData */
            $oBasketItemData = $this->getNew('paypPayPalPlusBasketItemData');
            $oBasketItemData->setBasketItem($oBasketItem);
            $iItemsQuantity = (int) $oBasketItemData->getQuantity();

            $aShopData['ItemList'][] = $oBasketItemData;
            $aShopData['Subtotal'] += $this->_round((double) $oBasketItemData->getPrice() * $iItemsQuantity);
            $aShopData['Tax'] += $this->_round((double) $oBasketItemData->getTax() * $iItemsQuantity);
        }

        return $aShopData;
    }

    /**
     * Calculate rounding error - a small difference between eShop and PayPal subtotal calculation rules.
     * This also includes any other differences as VAT amounts for some costs or discounts.
     * It will be used to adjust tax amount and keep balance correct.
     *
     * @param array $aShopData
     *
     * @return float
     */
    protected function _calculatePayPalTaxError(array $aShopData)
    {
        $oUtils = $this->getDataUtils();

        $dDiff = $this->_round($oUtils->getArrayValue('Total', $aShopData)) -
                 $this->_round($oUtils->getArrayValue('Subtotal', $aShopData)) -
                 $this->_round($oUtils->getArrayValue('Tax', $aShopData)) -
                 $this->_round($oUtils->getArrayValue('HandlingFee', $aShopData)) -
                 $this->_round($oUtils->getArrayValue('Insurance', $aShopData)) -
                 $this->_round($oUtils->getArrayValue('Shipping', $aShopData)) -
                 $this->_round($oUtils->getArrayValue('ShippingDiscount', $aShopData));

        return $dDiff;
    }

    /**
     * Round float number ith two signs precision (same as PayPal API does).
     *
     * @param float $dValue
     *
     * @return float
     */
    protected function _round($dValue)
    {
        return round((double) $dValue, 2);
    }
}
