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
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

/**
 * Class paypPayPalPlusOxOrder.
 * Overloads oxOrder model.
 *
 * @see oxOrder
 */
class paypPayPalPlusOxOrder extends paypPayPalPlusOxOrder_parent
{

    const ORDER_UNPAID_STRING = '0000-00-00 00:00:00';

    /**
     * Instance of the PayPal Plus Payment related to this order.
     *
     * @var paypPayPalPlusPaymentData $_oOrderPayment
     */
    protected $_oOrderPayment;

    /**
     * @inheritdoc
     *
     * In PayPal Plus pending PayPal transaction cannot be handled automatically yet.
     * Due to this fact the order has to be set to paid manually, when a pending transaction is completed.
     * After setting the order to paid, the payment the status of the PayPalPayment has to be set to completed.
     */
    public function save()
    {
        if (($blSave = parent::save())) {
            if ($this->_orderWasPaidWithPayPalPlus()) {
                $this->_setPayPalPaymentPlusStatusCompleted();
            }
        }

        return $blSave;
    }

    /**
     * Overloaded parent method.
     * Deletes PayPal Plus payment entry (paypPayPalPlusPaymentData model) related to an order.
     *
     * @inheritDoc
     */
    public function delete($sOxId = null)
    {
        /** @var paypPayPalPlusOxOrder|oxOrder $this */

        $blOrderDeleted = $this->_paypPayPalPlusOxOrder_delete_parent($sOxId);

        if (!empty($blOrderDeleted)) {

            if (is_null($sOxId)) {
                $sOxId = $this->getId();
            }

            /** @var paypPayPalPlusPaymentData $oPayPalPaymentData */
            $oPayPalPaymentData = paypPayPalPlusShop::getShop()->getNew('paypPayPalPlusPaymentData');

            if ($oPayPalPaymentData->loadByOrderId($sOxId)) {
                return $oPayPalPaymentData->delete();
            }
        }

        return $blOrderDeleted;
    }

    /**
     * Set date and time when order was paid.
     * It acts also as a status marking an order as paid.
     *
     * @param string $sDateAndTime
     */
    public function setPaymentDateAndTime($sDateAndTime)
    {
        $this->oxorder__oxpaid = new oxField((string) $sDateAndTime);
    }

    /**
     * Get Payment Instructions, if this payment was made with PayPal Plus Payment upon Invoice.
     * This is needed by the email templates.
     *
     * @return null|paypPayPalPlusPuiData
     */
    public function getPaymentInstructions()
    {
        $oPaymentInstructions = null;

        $oPayment = $this->getOrderPayment();
        if ($oPayment) {
            $sPaymentId = $oPayment->getPaymentId();

            $oPayPalPlusPuiData = oxNew('paypPayPalPlusPuiData');
            $oPayPalPlusPuiData->loadByPaymentId($sPaymentId);
            if ($oPayPalPlusPuiData->isLoaded()) {
                $oPaymentInstructions = $oPayPalPlusPuiData;
            }
        }

        return $oPaymentInstructions;
    }

    /**
     * Get PayPal Plus Payment data object related to current order.
     *
     * @return null|paypPayPalPlusPaymentData
     */
    public function getOrderPayment()
    {
        if (is_null($this->_oOrderPayment)) {

            /** @var paypPayPalPlusPaymentData $oPayPalPaymentData */
            $oPaymentData = paypPayPalPlusShop::getShop()->getNew('paypPayPalPlusPaymentData');

            if ($oPaymentData->loadByOrderId($this->getId())) {
                $this->_oOrderPayment = $oPaymentData;
            }
        }

        return $this->_oOrderPayment;
    }

    /**
     * Add a refund to the discount of the order.
     *
     * @param \PayPal\Api\Amount $oAmount
     *
     * @return bool
     * @throws Exception
     */
    public function discountRefund(\PayPal\Api\Amount $oAmount)
    {

        $this->_isRefundDiscountable($oAmount);

        $aParams = array(
            'oxorder__oxdiscount' => $this->_getNewTotalDiscount($oAmount),
        );

        /**  @var false|null $blResult Returns null on success, thus we must test for identical false */
        $blResult = $this->assign($aParams);
        if (false === $blResult) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_DISCOUNT_COULD_NOT_BE_ASSIGNED_TO_ORDER';
            $this->_throwRefundException($sMessage);
        }

        /** Do not reload Delivery costs, this is not necessary */
        $this->reloadDelivery(false);
        /** Do not reload basket discounts, this is not necessary. We have an order discount here */
        $this->reloadDiscount(false);

        /**
         * Recalculation of the order will do all the maths.
         * The new discount will be subtracted from brut and VAT Sum + Net Sum is recalculated
         * This function silently fails on error, but of course we are curious if our request was successful ...
         */
        $this->recalculateOrder();

        /** ... so we check the success like this */
        $blSuccess = $aParams['oxorder__oxdiscount'] == (double) $this->oxorder__oxdiscount->value;

        return $blSuccess;
    }

    /**
     * Return true if an invoice for this order has been generated in OXID.
     *
     * @return bool
     */
    public function hasInvoice()
    {
        $blHasInvoice = false;
        // BillNr (varchar) or InvoiceNr (int) is present
        if ($this->oxorder__oxbillnr->value || $this->oxorder__oxinvoicenr->value) {
            $blHasInvoice = true;
        }

        return $blHasInvoice;
    }

    /**
     * Template getter.
     * Returns true if the order has Payment instructions (i.e. PuI payment) else false.
     *
     * @return bool
     */
    public function hasPaymentInstructions()
    {
        $oPaymentInstructions = $this->getPaymentInstructions();

        return !is_null($oPaymentInstructions);
    }

    /**
     * Template getter.
     * Get the name of the payment type.
     *
     * @return string
     */
    public function getPaymentName()
    {
        $sPaymentName = '';

        $sPaymentType = $this->oxorder__oxpaymenttype->value;
        $oPayment = oxNew('oxPayment');
        if ($oPayment->load($sPaymentType)) {
            $sPaymentName = $oPayment->oxpayments__oxdesc->value;
        }

        return $sPaymentName;
    }

    /**
     * Get the next free order number from oxCounter. This number is persisted in oxCounter table,
     * but not yet in oxOrder table.
     * Persisting this number will be done, when the order is persisted via order->finalizeOrder.
     * The call to getNextOrderNr() is done in \paypPayPalPlusPayment::_createPayment resp.
     * \paypPayPalPlusPayment::_updatePayment and as oxOrder::finalizeOrder()
     * may not necessary be called the may be unused order ids.
     *
     * This is normally done in oxOrder::finalizeOrder, but we need the possibility to do this before, as we have to
     * send the order number to PayPal.
     *
     * @return bool
     */
    public function getNextOrderNr()
    {
        $sOrderNr = null;

        if ($this->_setNumber()) {
            $sOrderNr = $this->oxorder__oxordernr->value;
        }

        return $sOrderNr;
    }

    /**
     * If an invoice has been created the refund should not be discounted
     * as this would alter the data the invoice is based on.
     *
     * @param \PayPal\Api\Amount $oAmount
     *
     * @throws Exception
     */
    protected function _isRefundDiscountable(\PayPal\Api\Amount $oAmount)
    {

        // currency is not the same
        $oOrderCurrency = $this->getOrderCurrency();
        if ($oAmount->getCurrency() !== $oOrderCurrency->name) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_DISCOUNT_CURRENY_DIFFERS_FROM_ORDER_CURRENCY';
            $this->_throwRefundException($sMessage);
        }

        // current discount is greater than total order sum ( i.e. amount left to pay, after all past discounts ).
        if ((double) $oAmount->getTotal() > (double) $this->oxorder__oxtotalordersum->value) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_DISCOUNT_IS_BIGGER_THAN_TOTAL';
            $this->_throwRefundException($sMessage);
        }
    }

    /**
     * Get the new total discount of an order taking into account an amount refunded in PayPal Plus.
     *
     * @param \PayPal\Api\Amount $oAmount
     *
     * @return double
     */
    protected function _getNewTotalDiscount(\PayPal\Api\Amount $oAmount)
    {
        return (double) $oAmount->getTotal() + (double) $this->oxorder__oxdiscount->value;
    }

    /**
     * Throw a paypPayPalPlusRefundException exception.
     *
     * @param $sMessage
     *
     * @throws paypPayPalPlusRefundException
     */
    protected function _throwRefundException($sMessage)
    {
        $oEx = oxNew('paypPayPalPlusRefundException');
        $oEx->setMessage($sMessage);

        throw $oEx;
    }

    /**
     * Parent `delete` call. Method required for mocking.
     *
     * @inheritDocs
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusOxOrder_delete_parent($sOxId)
    {
        return parent::delete($sOxId);
    }

    /**
     * Set the status of the related PayPalPlus Payment to completed
     */
    protected function _setPayPalPaymentPlusStatusCompleted()
    {
        /** Get the string for the completed status */
        $sStatusCompleted = paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getRefundablePaymentStatus();

        /** Get the orders' PayPal Plus Payment data */
        $oPayPalPlusPaymentData = $this->getOrderPayment();
        /** If the users just played around, there might not be a PayPalPlusPayment for this order */
        if ($oPayPalPlusPaymentData) {
            $oPayPalPlusPaymentData->setStatus($sStatusCompleted);
            if (!$oPayPalPlusPaymentData->save()) {
                $sMessage = 'PAYP_PAYPALPLUS_ERR_PAYMENTSTATUS_NOT_UPDATED';
                $this->_setErrorMessage($sMessage);
            }
        }
    }

    /**
     * Return true if the order was paid via PayPal Plus
     *
     * @return bool
     */
    protected function _orderWasPaidWithPayPalPlus()
    {
        $blOrderIsPaid = $this->_isOrderPaid();
        $blOrderPaymentTypeIsPayPalPlus = paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId() == $this->oxorder__oxpaymenttype->value;

        return $blOrderIsPaid && $blOrderPaymentTypeIsPayPalPlus;
    }

    /**
     * Return true if the order is paid.
     *
     * @return bool
     */
    protected function _isOrderPaid()
    {
        $sPaid = (string) $this->oxorder__oxpaid->value;

        return $sPaid !== paypPayPalPlusOxOrder::ORDER_UNPAID_STRING;
    }

    /**
     * Needed for testing
     *
     * @codeCoverageIgnore
     *
     * @param $sMessage
     */
    protected function _setErrorMessage($sMessage)
    {
        oxRegistry::get("oxUtilsView")->addErrorToDisplay($sMessage);
    }

    /**
     * Override exportStandart function from invoicePdf module to be able to add
     * custom text with custom functions to the generated PDF.
     *
     * @see \paypPayPalPlusPdfArticleSummary::_setPayUntilInfo for shop versions CE/PE 4.7, 4.8
     * @see \paypPayPalPlusInvoicePdfArticleSummary::_setPayUntilInfo for shop versions CE/PE 4.9
     *
     * @param object $oPdf
     */
    public function exportStandart($oPdf)
    {
        $sPdfBlockClass = class_exists('PdfBlock') ? 'PdfBlock' : 'InvoicepdfBlock';

        $sShopVersion = oxRegistry::getConfig()->getVersion();
        if (version_compare($sShopVersion, "4.9.0", "<")) {
            $sPdfArticleSummaryClass = 'paypPayPalPlusPdfArticleSummary';
        } else {
            $sPdfArticleSummaryClass = 'paypPayPalPlusInvoicePdfArticleSummary';
        }

        // preparing order curency info
        $myConfig = $this->getConfig();
        $oPdfBlock = new $sPdfBlockClass();

        $this->_oCur = $myConfig->getCurrencyObject($this->oxorder__oxcurrency->value);
        if (!$this->_oCur) {
            $this->_oCur = $myConfig->getActShopCurrencyObject();
        }

        // loading active shop
        $oShop = $this->_getActShop();

        // shop information
        $oPdf->setFont($oPdfBlock->getFont(), '', 6);
        $oPdf->text(15, 55, $oShop->oxshops__oxname->getRawValue() . ' - ' . $oShop->oxshops__oxstreet->getRawValue() . ' - ' . $oShop->oxshops__oxzip->value . ' - ' . $oShop->oxshops__oxcity->getRawValue());

        // billing address
        $this->_setBillingAddressToPdf($oPdf);

        // delivery address
        if ($this->oxorder__oxdelsal->value) {
            $this->_setDeliveryAddressToPdf($oPdf);
        }

        // loading user
        $oUser = oxNew('oxuser');
        $oUser->load($this->oxorder__oxuserid->value);

        // user info
        $sText = $this->translate('ORDER_OVERVIEW_PDF_FILLONPAYMENT');
        $oPdf->setFont($oPdfBlock->getFont(), '', 5);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), 55, $sText);

        // customer number
        $sCustNr = $this->translate('ORDER_OVERVIEW_PDF_CUSTNR') . ' ' . $oUser->oxuser__oxcustnr->value;
        $oPdf->setFont($oPdfBlock->getFont(), '', 7);
        $oPdf->text(195 - $oPdf->getStringWidth($sCustNr), 59, $sCustNr);

        // setting position if delivery address is used
        if ($this->oxorder__oxdelsal->value) {
            $iTop = 115;
        } else {
            $iTop = 91;
        }

        // shop city
        if ($this->oxorder__oxbilldate->value) {
            $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y', strtotime($this->oxorder__oxbilldate->value));
        } else {
            $sText = $oShop->oxshops__oxcity->getRawValue() . ', ' . date('d.m.Y');
        }
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // shop VAT number
        if ($oShop->oxshops__oxvatnumber->value) {
            $sText = $this->translate('ORDER_OVERVIEW_PDF_TAXIDNR') . ' ' . $oShop->oxshops__oxvatnumber->value;
            $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 12, $sText);
            $iTop += 8;
        } else {
            $iTop += 4;
        }

        // invoice number
        $sText = $this->translate('ORDER_OVERVIEW_PDF_COUNTNR') . ' ' . $this->oxorder__oxbillnr->value;
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop + 8, $sText);

        // marking if order is canceled
        if ($this->oxorder__oxstorno->value == 1) {
            $this->oxorder__oxordernr->setValue($this->oxorder__oxordernr->getRawValue() . '   ' . $this->translate('ORDER_OVERVIEW_PDF_STORNO'), oxField::T_RAW);
        }

        // order number
        $oPdf->setFont($oPdfBlock->getFont(), '', 12);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_PURCHASENR') . ' ' . $this->oxorder__oxordernr->value);

        // order date
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);
        $aOrderDate = explode(' ', $this->oxorder__oxorderdate->value);
        $sOrderDate = oxRegistry::get("oxUtilsDate")->formatDBDate($aOrderDate[0]);
        $oPdf->text(15, $iTop + 8, $this->translate('ORDER_OVERVIEW_PDF_ORDERSFROM') . $sOrderDate . $this->translate('ORDER_OVERVIEW_PDF_ORDERSAT') . $oShop->oxshops__oxurl->value);
        $iTop += 16;

        // product info header
        $oPdf->setFont($oPdfBlock->getFont(), '', 8);
        $oPdf->text(15, $iTop, $this->translate('ORDER_OVERVIEW_PDF_AMOUNT'));
        $oPdf->text(30, $iTop, $this->translate('ORDER_OVERVIEW_PDF_ARTID'));
        $oPdf->text(45, $iTop, $this->translate('ORDER_OVERVIEW_PDF_DESC'));
        $oPdf->text(135, $iTop, $this->translate('ORDER_OVERVIEW_PDF_VAT'));
        $oPdf->text(148, $iTop, $this->translate('ORDER_OVERVIEW_PDF_UNITPRICE'));
        $sText = $this->translate('ORDER_OVERVIEW_PDF_ALLPRICE');
        $oPdf->text(195 - $oPdf->getStringWidth($sText), $iTop, $sText);

        // separator line
        $iTop += 2;
        $oPdf->line(15, $iTop, 195, $iTop);

        // #345
        $siteH = $iTop;
        $oPdf->setFont($oPdfBlock->getFont(), '', 10);

        // order articles
        $this->_setOrderArticlesToPdf($oPdf, $siteH, true);

        // generating pdf file
        $oArtSumm = new $sPdfArticleSummaryClass($this, $oPdf);
        $iHeight = $oArtSumm->generate($siteH);
        if ($siteH + $iHeight > 258) {
            $this->pdfFooter($oPdf);
            $iTop = $this->pdfHeader($oPdf);
            $oArtSumm->ajustHeight($iTop - $siteH);
            $siteH = $iTop;
        }

        $oArtSumm->run($oPdf);
        $siteH += $iHeight + 8;

        $oPdf->text(15, $siteH, $this->translate('ORDER_OVERVIEW_PDF_GREETINGS'));
    }
}
