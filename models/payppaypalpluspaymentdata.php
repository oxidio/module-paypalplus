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
 * Class paypPayPalPlusPaymentData.
 * PayPal Plus payment data model.
 */
class paypPayPalPlusPaymentData extends oxBase
{

    /**
     * OXID eShop order object associated with the payment.
     *
     * @var null|oxOrder
     */
    protected $_oOrder = null;

    /**
     * List of refunds associated with the payment.
     *
     * @var null|paypPayPalPlusRefundDataList
     */
    protected $_oRefundList = null;

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;

    /**
     * A sum of refunded amounts for the payment.
     *
     * @var null|double
     */
    protected $_dTotalAmountRefunded = null;


    /**
     * Methods that qualify for a status update.
     * Only this methods may initialize a update of the status field.
     *
     * This must be a subset of \paypPayPalPlusConfig::$_aSubscribedEventTypes
     *
     * @see \paypPayPalPlusConfig::$_aSubscribedEventTypes
     *
     * @var array
     */
    protected $_statusUpdateQualifiedMethods = array('sale');

    /**
     * States that qualify for a status update.
     * Only this methods may initialize a update of the status field.
     * This must be a subset of \paypPayPalPlusConfig::$_aSubscribedEventTypes
     *
     * @see \paypPayPalPlusConfig::$_aSubscribedEventTypes
     *
     * @var array
     */
    protected $_statusUpdateQualifiedStates = array('completed', 'pending', 'refunded', 'reversed');

    /**
     * Wrapper property for PayPal completed status
     *
     * @var array
     */
    protected $_sPayPalStatusCompleted = 'completed';

    /**
     * Class constructor, initiates parent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->init('payppaypalpluspayment');
    }


    /**
     * Set OXID eShop oxOrder model primary key value.
     *
     * @param string $sOrderId
     */
    public function setOrderId($sOrderId)
    {
        $this->payppaypalpluspayment__oxorderid = new oxField($sOrderId);
    }

    /**
     * Get OXID eShop oxOrder model primary key value.
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->payppaypalpluspayment__oxorderid->value;
    }

    /**
     * Set PayPal Plus Payment model sale ID.
     *
     * @param string $sSaleId
     */
    public function setSaleId($sSaleId)
    {
        $this->payppaypalpluspayment__oxsaleid = new oxField($sSaleId);
    }

    /**
     * Get PayPal Plus Payment model sale ID.
     *
     * @return string
     */
    public function getSaleId()
    {
        return $this->payppaypalpluspayment__oxsaleid->value;
    }

    /**
     * Set PayPal Plus payment (transaction) ID.
     *
     * @param string $sPaymentId
     */
    public function setPaymentId($sPaymentId)
    {
        $this->payppaypalpluspayment__oxpaymentid = new oxField($sPaymentId);
    }

    /**
     * Get PayPal Plus payment (transaction) ID.
     *
     * @return string
     */
    public function getPaymentId()
    {
        return $this->payppaypalpluspayment__oxpaymentid->value;
    }

    /**
     * Set PayPal Plus Payment sale status.
     *
     * @param string $sStatus
     */
    public function setStatus($sStatus)
    {
        $this->payppaypalpluspayment__oxstatus = new oxField($sStatus);
    }

    /**
     * Get PayPal Plus Payment sale status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->payppaypalpluspayment__oxstatus->value;
    }

    /**
     * Set PayPal Plus Payment creation date and time.
     *
     * @param string $sDate
     */
    public function setDateCreated($sDate)
    {
        $this->payppaypalpluspayment__oxdatecreated = new oxField($sDate);
    }

    /**
     * Get PayPal Plus Payment creation date and time.
     *
     * @return string
     */
    public function getDateCreated()
    {
        return $this->payppaypalpluspayment__oxdatecreated->value;
    }

    /**
     * Set PayPal Plus Payment grand total amount.
     *
     * @param double $dTotal
     */
    public function setTotal($dTotal)
    {
        $this->payppaypalpluspayment__oxtotal = new oxField((double) $dTotal);
    }

    /**
     * Get PayPal Plus Payment grand total amount.
     *
     * @return double
     */
    public function getTotal()
    {
        return $this->payppaypalpluspayment__oxtotal->value;
    }

    /**
     * Set PayPal Plus Payment currency code related to the total amount.
     *
     * @param string $sCurrency
     */
    public function setCurrency($sCurrency)
    {
        $this->payppaypalpluspayment__oxcurrency = new oxField($sCurrency);
    }

    /**
     * Get PayPal Plus Payment currency code related to the total amount.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->payppaypalpluspayment__oxcurrency->value;
    }

    /**
     * Set serialized PayPal Plus Payment object.
     *
     * @param \PayPal\Api\Payment $oPayment
     */
    public function setPaymentObject(PayPal\Api\Payment $oPayment)
    {
        $this->payppaypalpluspayment__oxpaymentobject = new oxField($oPayment->toJSON(), oxField::T_RAW);
    }

    /**
     * Get PayPal Plus Payment object un-serialized.
     *
     * @return bool|\PayPal\Api\Payment
     */
    public function getPaymentObject()
    {
        $oPayment = null;

        if ($this->payppaypalpluspayment__oxpaymentobject instanceof oxField) {
            try {
                $oSdk = $this->getShop()->getFromRegistry('paypPayPalPlusSdk');
                $oPayment = $oSdk->newPayment();
                $oPayment->fromJson($this->payppaypalpluspayment__oxpaymentobject->getRawValue());
            } catch (Exception $e) {
            }
        }

        return $oPayment;
    }

    /**
     * Check if the payment can be refunded.
     *
     * @return bool
     */
    public function isRefundable()
    {
        return ($this->getStatus() === $this->getShop()->getPayPalPlusConfig()->getRefundablePaymentStatus());
    }


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
     * Load entry by order ID.
     *
     * @param string $sOrderId
     *
     * @return bool
     */
    public function loadByOrderId($sOrderId)
    {
        return $this->_loadBy('OXORDERID', $sOrderId);
    }

    /**
     * Load entry by sale ID.
     *
     * @param string $sSaleId
     *
     * @return bool
     */
    public function loadBySaleId($sSaleId)
    {
        return $this->_loadBy('OXSALEID', $sSaleId);
    }

    /**
     * Load entry by payment ID.
     *
     * @param string $sPaymentId
     *
     * @return bool
     */
    public function loadByPaymentId($sPaymentId)
    {
        return $this->_loadBy('OXPAYMENTID', $sPaymentId);
    }


    /**
     * Get OXID eShop order associated with the PayPal Plus Payment.
     * Throw an exception if the order is not loaded (each payment must be based on an order).
     *
     * @return null|oxOrder
     * @throws oxException
     */
    public function getOrder()
    {
        if (is_null($this->_oOrder)) {

            /** @var oxOrder $oOrder */
            $oOrder = $this->getShop()->getNew('oxOrder');

            if ($oOrder->load($this->getOrderId())) {
                $this->_oOrder = $oOrder;
            } else {
                $this->_throwCouldNotLoadOrderError();
            }
        }

        return $this->_oOrder;
    }

    /**
     * Get a list of related refunds by sale ID.
     *
     * @return null|paypPayPalPlusRefundDataList
     */
    public function getRefundsList()
    {
        if (is_null($this->_oRefundList)) {

            /** @var paypPayPalPlusRefundDataList $oRefundList */
            $oRefundList = $this->getShop()->getNew('paypPayPalPlusRefundDataList');
            $oRefundList->loadRefundsBySaleId($this->getSaleId());

            if ($oRefundList->count() > 0) {
                $this->_oRefundList = $oRefundList;
            }
        }

        return $this->_oRefundList;
    }

    /**
     * Calculate a total amount already refunded for the payment.
     *
     * @return double
     */
    public function getTotalAmountRefunded()
    {
        if (is_null($this->_dTotalAmountRefunded)) {

            /** @var paypPayPalPlusRefundDataList $oRefundList */
            $oRefundList = $this->getShop()->getNew('paypPayPalPlusRefundDataList');
            $this->_dTotalAmountRefunded = (double) $oRefundList->getRefundedSumBySaleId($this->getSaleId());
        }

        return $this->_dTotalAmountRefunded;
    }


    /**
     * Overloaded parent method.
     * Delete refunds associated with the payment on its deletion.
     *
     * @param null|string $sOxId
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if ($this->_paypPayPalPlusPaymentData_delete_parent($sOxId)) {

            /** @var paypPayPalPlusRefundData $oRefundData */
            $oRefundData = $this->getShop()->getNew('paypPayPalPlusRefundData');

            return (bool) $oRefundData->deleteBySaleId($this->getSaleId());
        }

        return false;
    }

    /**
     * Event type support
     *   The event types currently supported are as follows:
     *
     *   PAYMENT.AUTHORIZATION.CREATED: This event gets triggered when an authorization happens. This is when the payment authorization is created, approved, and executed. The other use case is when a future payment authorization is created.
     *   PAYMENT.AUTHORIZATION.VOIDED: This event gets triggered when an authorization is voided.
     *   PAYMENT.CAPTURE.COMPLETED: This event gets triggered when a capture is completed.
     *   PAYMENT.CAPTURE.PENDING: This event gets triggered when a capture goes into pending state.
     *   PAYMENT.CAPTURE.REFUNDED: This event gets triggered when a capture is refunded by the merchant.
     *   PAYMENT.CAPTURE.REVERSED: This event gets triggered when a capture is reversed by PayPal.
     *   PAYMENT.SALE.COMPLETED: This event gets triggered when the sale is completed.
     *   PAYMENT.SALE.PENDING: This event gets triggered when a sale goes into pending state.
     *   PAYMENT.SALE.REFUNDED: This event gets triggered when the sale is refunded by the merchant.
     *   PAYMENT.SALE.REVERSED: This event gets triggered when the sale is reversed by PayPal.
     *   RISK.DISPUTE.CREATED: This event gets triggered when a dispute is created.
     *
     * @param $sEventType
     */
    public function setStatusByEventType($sEventType)
    {
        list($domain, $method, $status) = array_map('strtolower', explode('.', $sEventType));
        if ($domain == 'payment'
            && in_array($method, $this->_statusUpdateQualifiedMethods)
            && in_array($status, $this->_statusUpdateQualifiedStates)
        ) {
            $this->setStatus($status);
        }
    }

    /**
     * @param $sDatetime string A ISO_8601 date as coming from PayPal
     *
     * @return bool
     */
    public function setOrderPaid($sDatetime)
    {
        $oShop = $this->getShop();
        /** @var paypPayPalPlusOxOrder $oOrder */
        $oOrder = $this->getOrder();
        $oOrder->setPaymentDateAndTime(
            $oShop->getConverter()->date($sDatetime)
        );

        return (bool) $oOrder->save();
    }

    public function getPaymentInstructions()
    {
        $oPaymentInstructions = null;
        $oPayPalPlusPuiData = oxNew('paypPayPalPlusPuiData');
        if ($oPayPalPlusPuiData->loadByPaymentId($this->getPaymentId())) {
            $oPaymentInstructions = $oPayPalPlusPuiData;
        }

        return $oPaymentInstructions;
    }

    /**
     * Load entry by a field name and value.
     * Used for loading by `OXORDERID`, `OXSALEID` and `OXPAYMENTID`.
     *
     * @param string $sFieldName
     * @param string $sFieldValue
     *
     * @return bool
     */
    protected function _loadBy($sFieldName, $sFieldValue)
    {
        if (!in_array($sFieldName, array('OXORDERID', 'OXSALEID', 'OXPAYMENTID'))) {
            return false;
        }

        $sSelect = sprintf(
            "SELECT * FROM `%s` WHERE `%s` = %s",
            $this->getCoreTableName(),
            $sFieldName,
            $this->getShop()->getDb()->quote($sFieldValue)
        );
        $this->_isLoaded = $this->assignRecord($sSelect);

        return $this->_isLoaded;
    }

    /**
     * Throw an exception with "order not loaded" message.
     *
     * @throws oxException
     */
    protected function _throwCouldNotLoadOrderError()
    {
        /** @var paypPayPalPlusNoOrderException $oEx */
        $oEx = $this->getShop()->getNew('paypPayPalPlusNoOrderException');
        $oEx->setMessage($this->getShop()->translate('PAYP_PAYPALPLUS_ERROR_NO_ORDER'));

        throw $oEx;
    }

    /**
     * Parent `delete` call. Method required for mocking.
     *
     * @inheritDocs
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusPaymentData_delete_parent($sOxId)
    {
        return parent::delete($sOxId);
    }
}
