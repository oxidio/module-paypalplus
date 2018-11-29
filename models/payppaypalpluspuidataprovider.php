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
 * Class paypPayPalPlusPaymentDataProvider.
 * A data provider for paypPayPalPlusPaymentData model.
 *
 * @see paypPayPalPlusPaymentData
 */
class paypPayPalPlusPuiDataProvider extends paypPayPalPlusDataProvider
{

    /**
     * Fields names for paypPayPalPlusPuiData model.
     *
     * @var array
     */
    protected $_aFields = array(
        'PaymentId', 'ReferenceNumber', 'DueDate', 'Total', 'Currency', 'BankName', 'AccountHolder', 'Iban', 'Bic', 'PuiObject'
    );

    /**
     * OXID eShop order instance.
     *
     * @var null|oxOrder
     */
    protected $_oOrder = null;

    /**
     * PayPal SDK Payment instance.
     *
     * @var null|PayPal\Api\Payment
     */
    protected $_oPayment = null;

    /**
     * Initialize the data provider with data source objects.
     *
     * @param oxOrder             $oOrder
     * @param \PayPal\Api\Payment $oPayment
     */
    public function init(oxOrder $oOrder, PayPal\Api\Payment $oPayment)
    {
        $this->_oOrder = $oOrder;
        $this->_oPayment = $oPayment;
    }

    /**
     * Get all field values for paypPayPalPlusPaymentData model.
     *
     * @return array
     */
    public function getData()
    {
        $oUtils = $this->getDataUtils();
        $oConvert = $this->getConverter();

        /** @var PayPal\Api\Payment $oPayment */
        $oPayment = $this->_getSourceObject('PayPal\Api\Payment');

        return array(
            'PaymentInstructionInstructionType'=> $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getInstructionType'
                )
            ),
            'PaymentId' => $oConvert->string(
                $oUtils->invokeGet($oPayment, 'getId')
            ),
            'ReferenceNumber'=> $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getReferenceNumber'
                )
            ),
            'DueDate' => $oConvert->date(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getPaymentDueDate'
                )
            ),
            'Total' => $oConvert->price(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getAmount', 'getValue'
                )
            ),
            'Currency' => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getAmount', 'getCurrency'
                )
            ),
            'BankName' => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getRecipientBankingInstruction', 'getBankName'
                )
            ),
            'AccountHolder' => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getRecipientBankingInstruction', 'getAccountHolderName'
                )
            ),
            'Iban' => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getRecipientBankingInstruction', 'getInternationalBankAccountNumber'
                )
            ),
            'Bic' => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getPaymentInstruction', 'getRecipientBankingInstruction', 'getBankIdentifierCode'
                )
            ),
            'PuiObject' => $oUtils->invokeGet( $oPayment, 'getPaymentInstruction' )
        );
    }

    /**
     * Get OXID order object and related PayPal payment instance.
     *
     * @return array
     */
    protected function _getSources()
    {
        return array(
            'oxOrder'            => $this->_oOrder,
            'PayPal\Api\Payment' => $this->_oPayment,
        );
    }
}
