<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @category      module
 * @package       paypalplus
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

/**
 * Class paypPayPalPlusRefundHandler.
 * PayPal Plus Refund instance handler: creates refund object, fills it with data, executes refund requests, etc.
 */
class paypPayPalPlusRefundHandler extends paypPayPalPlusSuperCfg
{

    /**
     * PayPal Refund instance.
     *
     * @var null|PayPal\Api\Refund
     */
    protected $_oRefund = null;

    /**
     * Set refund instance.
     *
     * @param PayPal\Api\Refund $oRefund
     */
    public function setRefund(PayPal\Api\Refund $oRefund)
    {
        $this->_oRefund = $oRefund;
    }

    /**
     * Get refund instance.
     *
     * @return null|PayPal\Api\Refund
     */
    public function getRefund()
    {
        return $this->_oRefund;
    }

    /**
     * Initialize handler with a new PayPal Refund instance and fill its values.
     *
     * @param string $sTotal
     * @param string $sCurrency
     * @param string $sSaleId
     */
    public function init($sTotal, $sCurrency, $sSaleId)
    {
        /** @var PayPal\Api\Refund $oRefund */
        $oRefund = $this->getSdk()->newRefund();

        $this->_fill($oRefund, $sTotal, $sCurrency, $sSaleId);

        $this->setRefund($oRefund);
    }

    /**
     * Refund a payment of "sale" type - send refund transaction to PayPal API.
     *
     * @param \PayPal\Rest\ApiContext $oApiContext
     *
     * @return bool|string
     */
    public function refund(PayPal\Rest\ApiContext $oApiContext)
    {

        $oRefund = $this->getRefund();
        $sSaleId = $oRefund->getSaleId();
        $oOrder = $this->_getOrderBySaleId($sSaleId);

        $blOrderHasInvoice = $oOrder->hasInvoice();
        $blDiscountsEnabled = $this->getShop()->getPayPalPlusConfig()->getModuleSetting('DiscountRefunds');
        $blDisableRefundsOnOrderWithInvoice = $this->getShop()->getPayPalPlusConfig()->getModuleSetting('RefundOnInvoice');

        list($blDoRefund, $sRefundMessage) = $this->_getDoRefund($blDiscountsEnabled, $blOrderHasInvoice, $blDisableRefundsOnOrderWithInvoice);
        $blDoDiscount = $this->_getDoDiscount($blDiscountsEnabled);

        try {
            $mResult = false;
            if ($blDoRefund) {
                $mResult = $this->_doRefund($oApiContext, $oRefund);
            }
            if ($mResult && $blDoDiscount) {
                $this->_discountRefund($mResult);
            }

            if ($sRefundMessage) {
                $this->_throwRefundException($sRefundMessage);
            }
        } catch (Exception $oException) {
            $oErrorHandler = $this->getShop()->getErrorHandler();
            $oErrorHandler->debug($oException, $oRefund);

            return $oErrorHandler->parseError($oException, true);
        }

        return true;
    }

    /**
     * Return if a refund may be done or not, based on a set of rules applied to the method parameters.
     *
     * Return array
     * - first element is true if a refund may be done
     * - second element is a message string, explaining the decision, if necessary
     *
     * @param $blDiscountsEnabled
     * @param $blOrderHasInvoice
     * @param $blDisableRefundsOnOrderWithInvoice
     *
     * @return array
     */
    protected function _getDoRefund($blDiscountsEnabled, $blOrderHasInvoice, $blDisableRefundsOnOrderWithInvoice)
    {
        /** @var  bool $blDoRefund Defaults to true, if none of the rules is applied */
        $blDoRefund = true;
        $sMessage = '';

        /** If discounts are NOT enabled, or Order Has NO Invoice, no rules will be applied and refunds will be enabled  */
        if ($blDiscountsEnabled && $blOrderHasInvoice) {
            /** If an order has an invoice and refunds are NOT enabled any more, return false and set a message */
            if ($blDisableRefundsOnOrderWithInvoice) {
                $blDoRefund = false;
                $sMessage = 'PAYP_PAYPALPLUS_ERR_INVOICE_EXISTS';
            /** If an order has an invoice and refunds are sill enabled, return true and set a message */
            } else {
                $blDoRefund = true;
                $sMessage = 'PAYP_PAYPALPLUS_ERR_INVOICE_UPDATE_REQUIRED';
            }
        }

        return array($blDoRefund, $sMessage);
    }

    /**
     * At the moment just returns the input value and is just needed for testing.
     *
     * @codeCoverageIgnore
     *
     * @param bool $blDiscountsEnabled
     *
     * @return bool
     */
    protected function _getDoDiscount($blDiscountsEnabled) {
        return $blDiscountsEnabled;
    }

    /**
     * Fill refund instance with mandatory values to make a refund request.
     * Create PayPal API objects, links then and fills with data (payment/sale and request data).
     *
     * @param PayPal\Api\Refund $oRefund
     * @param string            $sTotal
     * @param string            $sCurrency
     * @param string            $sSaleId
     */
    protected function _fill(PayPal\Api\Refund $oRefund, $sTotal, $sCurrency, $sSaleId)
    {
        $oSdk = $this->getSdk();

        /** @var PayPal\Api\Amount $oAmount */
        $oAmount = $oSdk->newAmount();
        $oAmount->setTotal($sTotal);
        $oAmount->setCurrency($sCurrency);

        $oRefund->setAmount($oAmount);
        $oRefund->setSaleId($sSaleId);
    }

    /**
     * Collect PayPal Refund data and save it to OXID eShop data model.
     *
     * @param PayPal\Api\Refund $oRefund
     *
     * @return bool|string
     */
    protected function _saveRefundData(PayPal\Api\Refund $oRefund)
    {
        $oShop = $this->getShop();

        /** @var paypPayPalPlusRefundDataProvider $oRefundDataProvider */
        $oRefundDataProvider = $oShop->getNew('paypPayPalPlusRefundDataProvider');
        $oRefundDataProvider->init($oRefund);

        /** @var paypPayPalPlusRefundData $oRefundData */
        $oRefundData = $oShop->getNew('paypPayPalPlusRefundData');
        $oShop->getDataAccess()->transfuse($oRefundDataProvider, $oRefundData, $oRefundDataProvider->getFields());

        return $oRefundData->save();
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
     * Discount a refund off an order.
     *
     * @param string $sOxid OXID of the refundData
     *
     * @throws paypPayPalPlusRefundException
     */
    protected function _discountRefund($sOxid)
    {
        /**
         * Get the refund data from the database.
         *
         * @var  $oRefundData
         */
        $oRefundData = $this->_getRefundDataById($sOxid);

        /**
         * Get the refunds' Amount object
         */
        $oPayPalRefundObject = $this->_getPayPalApiRefundObject($oRefundData);
        $oAmount = $oPayPalRefundObject->getAmount();

        /**
         * Get order related to this sale
         */
        $sSaleId = $oRefundData->getSaleId();
        $oOrder = $this->_getOrderBySaleId($sSaleId);

        /**
         * Discount the refund off the order
         */
        if (!$oOrder->discountRefund($oAmount)) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_REFUND_COULD_NOT_BE_DISCOUNTED';
            $this->_throwRefundException($sMessage);
        }
    }

    /**
     * Return an order related to a given sale id.
     *
     * @param $sSaleId
     *
     * @return paypPayPalPlusOxOrder
     * @throws paypPayPalPlusRefundException
     */
    protected function _getOrderBySaleId($sSaleId)
    {
        $oPayPalPlusPaymentData = $this->_getPaymentDataBySaleId($sSaleId);

        $sOrderId = $oPayPalPlusPaymentData->getOrderId();

        /** @var paypPayPalPlusOxOrder $oOrder */
        $oOrder = oxNew('oxOrder');
        if (!$oOrder->load($sOrderId)) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_ORDER_COULD_NOT_BE_LOADED_FROM_DATABASE';
            $this->_throwRefundException($sMessage);
        }

        return $oOrder;
    }

    /**
     * Load and return paypPayPalPlusRefundData by its OXID.
     *
     * @param $sOxid
     *
     * @return paypPayPalPlusRefundData
     * @throws paypPayPalPlusRefundException
     */
    protected function _getRefundDataById($sOxid)
    {
        $oShop = $this->getShop();
        $oRefundData = $oShop->getNew('paypPayPalPlusRefundData');
        if (!$oRefundData->load($sOxid)) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_REFUND_DATA_COULD_NOT_BE_LOADED_FROM_DATABASE';
            $this->_throwRefundException($sMessage);
        }

        return $oRefundData;
    }

    /**
     * Load and return paypPayPalPlusPaymentData by a PayPal SaleId.
     *
     * @param $sSaleId
     *
     * @return paypPayPalPlusPaymentData
     * @throws paypPayPalPlusRefundException
     */
    protected function _getPaymentDataBySaleId($sSaleId)
    {
        $oPayPalPlusPaymentData = oxNew('paypPayPalPlusPaymentData');
        $oPayPalPlusPaymentData->loadBySaleId($sSaleId);
        if (!$oPayPalPlusPaymentData->isLoaded()) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_PAYMENT_DATA_COULD_NOT_BE_LOADED_FROM_DATABASE';
            $this->_throwRefundException($sMessage);
        }

        return $oPayPalPlusPaymentData;
    }

    /**
     * Restore the PayPal API Refund Object as stored in the database.
     *
     * @param paypPayPalPlusRefundData $oRefundData
     *
     * @return bool|\PayPal\Api\Refund
     * @throws paypPayPalPlusRefundException
     */
    protected function _getPayPalApiRefundObject(paypPayPalPlusRefundData $oRefundData)
    {
        $oPayPalRefundObject = $oRefundData->getRefundObject();
        if (!$oPayPalRefundObject instanceof \PayPal\Api\Refund) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_REFUND_OBJECT_COULD_NOT_BE_RESTORED';
            $this->_throwRefundException($sMessage);
        }

        return $oPayPalRefundObject;
    }

    /**
     * @param \PayPal\Rest\ApiContext $oApiContext
     * @param                         $oRefund
     *
     * @return string|bool OXID of the refundData or false on failure
     */
    protected function _doRefund(PayPal\Rest\ApiContext $oApiContext, $oRefund)
    {
        /** @var PayPal\Api\Sale $oSale */
        $oSale = $this->getSdk()->newSale();
        $oSale->setId($oRefund->getSaleId());
        $oExecutedRefund = $oSale->refund($oRefund, $oApiContext);

        $mResult = $this->_saveRefundData($oExecutedRefund);
        if (false === $mResult) {
            $sMessage = 'PAYP_PAYPALPLUS_ERROR_REFUND_DATA_COULD_NOT_BE_STORED_IN_DATABASE';
            $this->_throwRefundException($sMessage);
        }

        return $mResult;
    }
}