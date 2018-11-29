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
 * Class paypPayPalPlusOxBasket.
 * Overloads oxBasket model.
 *
 * @see oxBasket
 */
class paypPayPalPlusOxBasket extends paypPayPalPlusOxBasket_parent
{

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;


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
     * Get Payment ID value without trying to fetch it from session.
     *
     * @return mixed
     */
    public function getPaymentIdValue()
    {
        return $this->_sPaymentId;
    }

    /**
     * Get a hash based on serialized basket object.
     * Basket content and preselected payment cost are used,
     * since rest could be updated within PayPal payment patch call.
     *
     * @return string
     */
    public function getBasketHash()
    {
        /** @var paypPayPalPlusOxBasket|oxBasket $this */

        $oDataAccess = $this->getShop()->getDataAccess();
        $sBasketItemsIdentifier = '';
        $aItems = (array) $oDataAccess->invokeGet($this, 'getContents');

        foreach ($aItems as $oBasketItem) {

            /** @var oxBasketItem $oBasketItem */
            $sBasketItemsIdentifier .= (string) $oDataAccess->invokeGet($oBasketItem, 'getProductId') . '-' .
                                       (string) $oDataAccess->invokeGet($oBasketItem, 'getAmount') . ';';
        }

        $sSurchargeIdentifier = (string) (double) $oDataAccess->invokeGet($this, 'getPaymentCost', 'getPrice');

        return md5($sBasketItemsIdentifier . $sSurchargeIdentifier);
    }


    /**
     * Overloaded parent method.
     * Check if basket still matches the one related to approved payment in session.
     * If not, reset approved payment in session.
     *
     * @inheritDoc
     */
    public function afterUpdate()
    {
        /** @var paypPayPalPlusOxBasket|oxBasket $this */

        $this->_paypPayPalPlusOxBasket_afterUpdate_parent();

        $oPayPalSession = $this->getShop()->getPayPalPlusSession();
        $oApprovedPayment = $oPayPalSession->getApprovedPayment();

        if (!empty($oApprovedPayment) and ($oApprovedPayment instanceof PayPal\Api\Payment)) {
            $this->_resetApprovedPayment($oApprovedPayment, $oPayPalSession->getBasketStamp());
        }
    }


    /* Compatibility methods (missing on older shops) - START */

    /**
     * Overloaded parent method for newer shops (with no changes) - missing method in older shops.
     * Returns payment costs.
     *
     * @return oxPrice
     */
    public function getPaymentCost()
    {
        /** @var oxBasket $this */

        return $this->getCosts('oxpayment');
    }

    /**
     * Overloaded parent method for newer shops (with no changes) - missing method in older shops.
     * Gets total discount sum.
     *
     * @return float|int
     */
    public function getTotalDiscountSum()
    {
        /** @var oxBasket $this */

        $dPrice = 0;

        // subtracting total discount
        if ($oPrice = $this->getTotalDiscount()) {
            $dPrice += $oPrice->getPrice();
        }

        if ($oVoucherPrice = $this->getVoucherDiscount()) {
            $dPrice += $oVoucherPrice->getPrice();
        }

        return $dPrice;
    }

    /* Compatibility methods (missing on older shops) - END */


    /**
     * Check if current basket price and stamp still match approved payment.
     * If not, approved payment is invalidated.
     *
     * @param \PayPal\Api\Payment $oApprovedPayment
     * @param string              $sApprovedBasketStamp
     */
    protected function _resetApprovedPayment(PayPal\Api\Payment $oApprovedPayment, $sApprovedBasketStamp)
    {
        $oShop = $this->getShop();
        $oDataAccess = $oShop->getDataAccess();
        $oConverter = $oShop->getConverter();

        $mApprovedPrice = $oDataAccess->invokeGet($oApprovedPayment, 'getTransactions', '0:[]', 'getAmount', 'getTotal');
        $sBasketPrice = $oConverter->price($oDataAccess->invokeGet($this, 'getPrice', 'getPrice'));

        if (($sApprovedBasketStamp !== $this->getBasketHash()) or ($mApprovedPrice !== $sBasketPrice)) {
            $oShop->getPayPalPlusSession()->unsetApprovedPayment();
        }
    }


    /**
     * Parent `afterUpdate` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusOxBasket_afterUpdate_parent()
    {
        parent::afterUpdate();
    }

    /**
     * @inheritdoc
     *
     * If _sTsProductId is not set, try to get it from session
     * This is probably a bug in oxBasket::getTsProductId()
     *
     * @see oxBasket::getTsProductId()
     * 
     * @return string
     */
    public function getTsProductId()
    {
        $this->_sTsProductId = parent::getTsProductId();
        if (!$this->_sTsProductId) {
            $this->_sTsProductId = oxRegistry::getSession()->getVariable('stsprotection');
        }

        return $this->_sTsProductId;
    }

    /**
     *
     * Returns if an basket update is needed
     *
     *
     * @return bool
     */
    public function getUpdateNeeded()
    {
        return $this->_blUpdateNeeded;
    }

}
