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
 * Class paypPayPalPlusRefundDataProvider.
 * A data provider for paypPayPalPlusRefundData model.
 *
 * @see paypPayPalPlusRefundData
 */
class paypPayPalPlusRefundDataProvider extends paypPayPalPlusDataProvider
{

    /**
     * Fields names for paypPayPalPlusRefundData model.
     *
     * @var array
     */
    protected $_aFields = array('SaleId', 'RefundId', 'Status', 'DateCreated', 'Total', 'Currency', 'RefundObject');

    /**
     * PayPal SDK Refund instance.
     *
     * @var null|PayPal\Api\Refund
     */
    protected $_oRefund = null;


    /**
     * Initialize the data provider with a data source object.
     *
     * @param \PayPal\Api\Refund $oRefund
     */
    public function init(PayPal\Api\Refund $oRefund)
    {
        $this->_oRefund = $oRefund;
    }


    /**
     * Get all fields values for paypPayPalPlusRefundData model.
     *
     * @return array
     */
    public function getData()
    {
        $oDataAccess = $this->getDataUtils();
        $oConverter = $this->getConverter();

        /** @var PayPal\Api\Refund $oRefund */
        $oRefund = $this->_getSourceObject('PayPal\Api\Refund');

        return array(
            'SaleId'       => $oConverter->string($oDataAccess->invokeGet($oRefund, 'getSaleId')),
            'RefundId'     => $oConverter->string($oDataAccess->invokeGet($oRefund, 'getId')),
            'Status'       => $oConverter->string($oDataAccess->invokeGet($oRefund, 'getState')),
            'DateCreated'  => $oConverter->date($oDataAccess->invokeGet($oRefund, 'getCreateTime')),
            'Total'        => $oConverter->price($oDataAccess->invokeGet($oRefund, 'getAmount', 'getTotal')),
            'Currency'     => $oConverter->string($oDataAccess->invokeGet($oRefund, 'getAmount', 'getCurrency')),
            'RefundObject' => $this->_oRefund,
        );
    }


    /**
     * Get PayPal refund instance to fetch data from.
     *
     * @return array
     */
    protected function _getSources()
    {
        return array('PayPal\Api\Refund' => $this->_oRefund);
    }
}
