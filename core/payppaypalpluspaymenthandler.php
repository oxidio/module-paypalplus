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
 * Class paypPayPalPlusPaymentHandler.
 * PayPal Plus Payment instance handler: create payment object, fill it with data, execute payment, etc.
 */
class paypPayPalPlusPaymentHandler extends paypPayPalPlusSuperCfg
{

    /**
     * A name of checkout payment selection step controller.
     *
     * @var string
     */
    protected $_sPaymentController = 'payment';

    /**
     * A name of checkout order preview step controller.
     *
     * @var string
     */
    protected $_sOrderController = 'order';

    /**
     * PayPal Payment instance.
     *
     * @var null|PayPal\Api\Payment
     */
    protected $_oPayment = null;

    /**
     * Instance of PaymentData as stored in the database
     *
     * @var null|paypPayPalPlusPaymentData
     */
    protected $_oPaymentData = null;

    /**
     * Taxation handler instance.
     *
     * @var null|paypPayPalPlusTaxationHandler
     */
    protected $_oTaxationHandler = null;

    /**
     * A key standing for the approval URL in SDK Payment object links array.
     *
     * @var string
     */
    protected $_sApprovalUrlKey = 'approval_url';

    /**
     * Invoice number
     */
    protected $_sInvoiceNumber = '';

    /**
     * Set payment instance.
     *
     * @param PayPal\Api\Payment $oPayment
     */
    public function setPayment($oPayment)
    {
        $this->_oPayment = $oPayment;
    }

    /**
     * Get payment instance.
     *
     * @return null|PayPal\Api\Payment
     */
    public function getPayment()
    {
        return $this->_oPayment;
    }

    /**
     * Get models taxation handler instance.
     *
     * @return null|paypPayPalPlusTaxationHandler
     */
    public function getTaxationHandler()
    {
        if (is_null($this->_oTaxationHandler)) {
            $this->_oTaxationHandler = $this->getFromRegistry('paypPayPalPlusTaxationHandler');
        }

        return $this->_oTaxationHandler;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->_sInvoiceNumber;
    }

    /**
     * @param string $sInvoiceNumber
     */
    public function setInvoiceNumber($sInvoiceNumber)
    {
        $this->_sInvoiceNumber = $sInvoiceNumber;
    }

    /**
     * Initialize handler with a new PayPal Payment instance.
     * No user data is filled at this point.
     */
    public function init()
    {
        /** @var PayPal\Api\Payment $oPayment */
        $oPayment = $this->getSdk()->newPayment();
        $this->_fill($oPayment, false);

        $this->setPayment($oPayment);
    }

    /**
     * Create a payment - send payment transaction to PayPal API.
     *
     * @param \PayPal\Rest\ApiContext $oApiContext
     */
    public function create(PayPal\Rest\ApiContext $oApiContext)
    {
        try {
            $this->_addEstimatedDeliveryDate();
            $this->getPayment()->create($oApiContext);
        } catch (Exception $oException) {

            // Fail silently - because not yet sure if user would user PayPal method.
            $this->getShop()->getErrorHandler()->debug($oException, $this->getPayment());
        }
    }

    /**
     * Adds the estimated delivery date to the transaction.
     */
    protected function _addEstimatedDeliveryDate()
    {
        if (oxRegistry::getConfig()->getConfigParam("paypPayPalPlusEstimatedDeliveryDate")) {
            $oDataAccess = $this->getShop()->getDataAccess();
            $deliveryDate = $oDataAccess->getBasketDeliveryDate();

            if (!($this->getPayment()->transactions[0]->shipment_details instanceof \PayPal\Common\PayPalModel)) {
                $this->getPayment()->transactions[0]->shipment_details = new \PayPal\Common\PayPalModel();
            }
            $this->getPayment()->transactions[0]->shipment_details->estimated_delivery_date = $deliveryDate;
        }
    }

    /**
     * Update (or patch) a payment by providing additional customer information to PayPal API.
     *
     * @param \PayPal\Rest\ApiContext $oApiContext
     */
    public function update(PayPal\Rest\ApiContext $oApiContext)
    {
        $oPayment = $this->getPayment();
        $this->_fill($oPayment);

        try {
            $oPayment->update($this->_buildPaymentUpdatePatchRequest(), $oApiContext);
        } catch (Exception $oException) {
            $oErrorHandler = $this->getShop()->getErrorHandler();
            $oErrorHandler->debug($oException);
            $oErrorHandler->parseError($oException);
        }
    }

    /**
     * Execute (confirm or finalize) a payment within PayPal API and check the response for success status.
     *
     * @param string                        $sPayerId
     * @param \PayPal\Rest\ApiContext       $oApiContext
     * @param paypPayPalPlusOxOrder|oxOrder $oOrder
     *
     * @return bool
     */
    public function execute($sPayerId, PayPal\Rest\ApiContext $oApiContext, oxOrder $oOrder)
    {
        $oPayment         = $this->getPayment();
        $oExecutedPayment = null;

        /** @var PayPal\Api\PaymentExecution $oExecution */
        $oExecution = $this->getSdk()->newPaymentExecution();
        $oExecution->setPayerId($sPayerId);
        try {
            if (is_null($oPayment)) {
                $this->_throwInvalidArgumentException();
            }
            $oExecutedPayment = $oPayment->execute($oExecution, $oApiContext);
        } catch (Exception $oException) {
            // On failure just return false - error will be handled elsewhere
            $this->getShop()->getErrorHandler()->debug($oException);

            return false;
        }
        try {
            $this->_validatePayment($oOrder, $oExecutedPayment);
        } catch (Exception $oException) {
            // On failure just return false - error will be handled elsewhere
            $this->getShop()->getErrorHandler()->debug($oException);

            return false;
        }

        // Save payment data
        $oPaymentDataModel = $this->_savePaymentData($oOrder, $oExecutedPayment);

        // Set the order as paid if the payment was executed successfully
        $sTransactionState = $this->_getTransactionState($oOrder, $oExecutedPayment);
        if ($oPaymentDataModel && $this->_isSaleCompleted($sTransactionState)) {
            $sDatetime = $oExecutedPayment->getUpdateTime();
            if (is_null($sDatetime)) {
                $sDatetime = $oExecutedPayment->getCreateTime();
            }
            $oPaymentDataModel->setOrderPaid($sDatetime);
        }

        $sPaymentInstructionInstructionType = $this->_getPaymentInstructionInstructionType($oOrder, $oExecutedPayment);
        if ($this->_isSalePaymentUponInvoice($sPaymentInstructionInstructionType)) {
            $this->_savePaymentUponInvoiceData($oOrder, $oExecutedPayment);
        }

        $this->_logToFile($oExecutedPayment);

        return true;
    }

    /**
     * Get approval link from an assigned payment instance.
     *
     * @return string
     */
    public function getApprovalUrl()
    {
        $sApprovalLink = '';
        $oPayment      = $this->getPayment();

        if (!($oPayment instanceof PayPal\Api\Payment)) {
            return $sApprovalLink;
        }

        $aLinks = (array)$oPayment->getLinks();

        foreach ($aLinks as $oLink) {

            /** @var PayPal\Api\Links $oLink */
            if ($oLink->getRel() === $this->_sApprovalUrlKey) {
                $sApprovalLink = $oLink->getHref();
                break;
            }
        }

        return $sApprovalLink;
    }

    /**
     * Fill payment instance with values from eShop data providers.
     * Creates PayPal API objects, links then and fills with eShop data (current user and basket data).
     *
     * @param PayPal\Api\Payment $oPayment
     * @param bool               $blAddUserData Add customer data if True, skip customer data if False.
     */
    protected function _fill(PayPal\Api\Payment $oPayment, $blAddUserData = true)
    {
        $oSdk              = $this->getSdk();
        $oPayPalPlusConfig = $this->getShop()->getPayPalPlusConfig();

        /** @var paypPayPalPlusBasketData $oBasketData */
        $oBasketData = $this->getNew('paypPayPalPlusBasketData');
        $oItemList   = $this->_getItemList($oBasketData);

        if ($blAddUserData) {
            $this->_addUserData($oItemList);
        }

        /** @var PayPal\Api\Transaction $oTransaction */
        $oTransaction = $oSdk->newTransaction();
        $oTransaction->setAmount($this->_getAmount($oBasketData));
        $oTransaction->setInvoiceNumber($this->getInvoiceNumber());
        $oTransaction->setItemList($oItemList);

        /** @var PayPal\Api\Payer $oPayer */
        $oPayer = $oSdk->newPayer();
        $oPayer->setPaymentMethod($oPayPalPlusConfig->getPayerPaymentMethod());

        if ($blAddUserData) {
            $this->addPayerInfo($oSdk, $oPayer);
        }

        /** @var PayPal\Api\RedirectUrls $oRedirectUrls */
        $oRedirectUrls = $oSdk->newRedirectUrls();
        $sBaseUrl      = $this->getShop()->getPayPalPlusConfig()->getShopBaseLink();
        $oRedirectUrls->setCancelUrl(
            sprintf(
                '%scl=%s&%s=1',
                $sBaseUrl,
                $this->_sPaymentController,
                $oPayPalPlusConfig->getCancellationReturnParameter()
            )
        );
        $oRedirectUrls->setReturnUrl(
            sprintf(
                '%scl=%s&%s=1&%s=%s',
                $sBaseUrl,
                $this->_sOrderController,
                $oPayPalPlusConfig->getSuccessfulReturnParameter(),
                $oPayPalPlusConfig->getForcedPaymentParameter(),
                $oPayPalPlusConfig->getPayPalPlusMethodId()
            )
        );

        $experienceProfileId = $this->getShop()->getPayPalPlusConfig()->getModuleSetting('ExpProfileId');
        if (!empty($experienceProfileId)) {
            $oPayment->setExperienceProfileId($experienceProfileId);
        }

        $oPayment->setIntent($oPayPalPlusConfig->getPaymentIntent());
        $oPayment->setPayer($oPayer);
        $oPayment->setTransactions(array($oTransaction));
        $oPayment->setRedirectUrls($oRedirectUrls);
    }

    /**
     * Get SDK object Amount filled with basket data including Details object.
     *
     * @param paypPayPalPlusBasketData $oBasketData
     *
     * @return PayPal\Api\Amount
     */
    protected function _getAmount(paypPayPalPlusBasketData $oBasketData)
    {
        $oSdk        = $this->getSdk();
        $oDataAccess = $this->getShop()->getDataAccess();

        /** @var PayPal\Api\Details $oDetails */
        $oDetails = $oSdk->newDetails();
        $oDataAccess->transfuse($oBasketData, $oDetails, $oBasketData->getFields('Details'), 'DetailsValue');
        $oDetails = $this->getTaxationHandler()->adjustedTaxation($oDetails, 'Subtotal');

        /** @var PayPal\Api\Amount $oAmount */
        $oAmount = $oSdk->newAmount();
        $oDataAccess->transfuse($oBasketData, $oAmount, $oBasketData->getFields('Amount'), 'AmountValue');
        $oAmount->setDetails($oDetails);

        return $oAmount;
    }

    /**
     * Get SDK object ItemList filled with Item objects containing eShop basket items data.
     *
     * @param paypPayPalPlusBasketData $oBasketData
     *
     * @return PayPal\Api\ItemList
     */
    protected function _getItemList(paypPayPalPlusBasketData $oBasketData)
    {
        $oSdk = $this->getSdk();

        /** @var PayPal\Api\ItemList $oItemList */
        $oItemList    = $oSdk->newItemList();
        $aBasketItems = (array)$oBasketData->getItemList();

        foreach ($aBasketItems as $oBasketItemData) {
            /** @var paypPayPalPlusBasketItemData $oBasketItemData */

            /** @var PayPal\Api\Item $oItem */
            $oItem = $oSdk->newItem();
            $this->getShop()->getDataAccess()->transfuse($oBasketItemData, $oItem, $oBasketItemData->getFields());
            $oItem = $this->getTaxationHandler()->adjustedTaxation($oItem);

            $oItemList->addItem($oItem);
        }

        return $oItemList;
    }

    /**
     * Add user shipping data to item list instance.
     *
     * @param \PayPal\Api\ItemList $oItemList
     */
    protected function _addUserData(PayPal\Api\ItemList $oItemList)
    {
        $oDataAccess = $this->getShop()->getDataAccess();

        /** @var paypPayPalPlusUserData $oUserData */
        $oUserData = $this->getNew('paypPayPalPlusUserData');

        /** @var PayPal\Api\Address $oShippingAddress */
        $oShippingAddress = $this->getSdk()->newShippingAddress();
        $oDataAccess->transfuse(
            $oUserData,
            $oShippingAddress,
            $oUserData->getFields('Address'),
            'ShippingAddressValue'
        );

        $oItemList->setShippingAddress($oShippingAddress);
    }

    /**
     * Collect filled PayPal Payment data and create PayPal Patch Request object to use for Payment update API call.
     *
     * @return \PayPal\Api\PatchRequest
     */
    protected function _buildPaymentUpdatePatchRequest()
    {
        $oSdk        = $this->getSdk();
        $oDataAccess = $this->getShop()->getDataAccess();

        /** @var PayPal\Api\PatchRequest $oPatchRequest */
        $oPatchRequest = $oSdk->newPatchRequest();
        $aPaymentData  = (array)$oDataAccess->invokeGet($this->getPayment(), 'toArray');

        $aAmount = (array)$oDataAccess->invokeGet($aPaymentData, 'transactions:[]', '0:[]', 'amount:[]');

        if (!empty($aAmount)) {

            /** @var PayPal\Api\Patch $oPatch */
            $oPatch = $oSdk->newPatch();
            $oPatch->setOp('replace')->setPath('/transactions/0/amount')->setValue($aAmount);

            $oPatchRequest->addPatch($oPatch);
        }

        $aShippingAddress = (array)$oDataAccess->invokeGet(
            $aPaymentData,
            'transactions:[]',
            '0:[]',
            'item_list:[]',
            'shipping_address:[]'
        );

        if (!empty($aShippingAddress)) {

            /** @var PayPal\Api\Patch $oPatch */
            $oPatch = $oSdk->newPatch();
            $oPatch->setOp('add')->setPath('/transactions/0/item_list/shipping_address')->setValue(
                $aShippingAddress
            );

            $oPatchRequest->addPatch($oPatch);
        }

        if (oxRegistry::getConfig()->getConfigParam("paypPayPalPlusEstimatedDeliveryDate")) {
            $deliveryDate = $oDataAccess->getBasketDeliveryDate();
            /** @var PayPal\Api\Patch $oPatch */
            $oPatch = $oSdk->newPatch();
            $oPatch->setOp('add')->setPath('/transactions/0/shipment_details/estimated_delivery_date')->setValue($deliveryDate);

            $oPatchRequest->addPatch($oPatch);

        }

        $aPayerInfo = (array)$oDataAccess->invokeGet(
            $aPaymentData,
            'payer:[]',
            'payer_info:[]'
        );

        if (!empty($aPayerInfo)) {

            /** @var PayPal\Api\Patch $oPatch */
            $oPatch = $oSdk->newPatch();
            $oPatch->setOp('replace')->setPath('/payer/payer_info')->setValue($aPayerInfo);

            $oPatchRequest->addPatch($oPatch);
        }

        return $oPatchRequest;
    }


    /**
     * Collect PayPal Payment data and save to OXID eShop data model.
     *
     * @param oxOrder             $oOrder
     * @param \PayPal\Api\Payment $oPayment
     *
     * @return bool|paypPayPalPlusPaymentData
     */
    protected function _savePaymentData(oxOrder $oOrder, PayPal\Api\Payment $oPayment)
    {
        $oShop = $this->getShop();

        /** @var paypPayPalPlusPaymentDataProvider $oPaymentDataProvider */
        $oPaymentDataProvider = $oShop->getNew('paypPayPalPlusPaymentDataProvider');
        $oPaymentDataProvider->init($oOrder, $oPayment);

        /** Save SaleId to the oxorder table */
        $oOrder->oxorder__oxtransid = new oxField($oPaymentDataProvider->getSaleId());
        $mResult                    = $oOrder->save();

        if ($mResult) {
            /** @var paypPayPalPlusPaymentData $oPaymentData */
            $oPaymentDataModel = $oShop->getNew('paypPayPalPlusPaymentData');
            $oShop->getDataAccess()->transfuse(
                $oPaymentDataProvider,
                $oPaymentDataModel,
                $oPaymentDataProvider->getFields()
            );

            if ($mResult = $oPaymentDataModel->save()) {
                $mResult = $oPaymentDataModel;
            }
        }

        return $mResult;
    }

    /**
     * Persist the payment instructions in the data model.
     *
     * @param oxOrder             $oOrder
     * @param \PayPal\Api\Payment $oPayment
     *
     * @return bool|paypPayPalPlusPuiData|string
     */
    protected function _savePaymentUponInvoiceData(oxOrder $oOrder, PayPal\Api\Payment $oPayment)
    {
        $oShop = $this->getShop();

        /** @var paypPayPalPlusPuiDataProvider $oDataProvider */
        $oDataProvider = $oShop->getNew('paypPayPalPlusPuiDataProvider');
        $oDataProvider->init($oOrder, $oPayment);

        /** @var paypPayPalPlusPuiData $oDataModel */
        $oDataModel = $this->_getPayPalPlusPuiDataModel();
        $oShop->getDataAccess()->transfuse($oDataProvider, $oDataModel, $oDataProvider->getFields());

        if ($mResult = $oDataModel->save()) {
            $mResult = $oDataModel;
        }

        return $mResult;
    }

    /**
     * Test helper function.
     *
     * @codeCoverageIgnore
     *
     * @return paypPayPalPlusPuiData
     */
    protected function _getPayPalPlusPuiDataModel()
    {
        $oShop = $this->getShop();
        /** @var paypPayPalPlusPuiData $oDataModel */
        $oDataModel = $oShop->getNew('paypPayPalPlusPuiData');

        return $oDataModel;
    }

    /**
     * Serialize and log content to file.
     *
     * @todo (nice2have): Create a separate logger class, make file name configurable, also use for refunds.
     *
     * @param mixed $mContent
     *
     * @return int
     *
     * @codeCoverageIgnore
     */
    protected function _logToFile($mContent)
    {
        $sShopDir = (string)$this->getShop()->getSetting('sShopDir');

        if (!$this->getShop()->getPayPalPlusConfig()->getModuleSetting('SaveToFile')) {
            return 0;
        }

        return @file_put_contents(
            $sShopDir . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'paypalplus-executed-payments.log',
            sprintf('%s: %s' . PHP_EOL . PHP_EOL, date('Y-m-d H:i:s'), serialize($mContent)),
            FILE_APPEND
        );
    }

    /**
     * Validate data just before submitting to PayPal.
     * We do not want to force all users to adjust some data just because PayPal plus is installed #PSPPP-188.
     *
     * @retrun bool
     */
    public function validateUserData()
    {
        /** @var paypPayPalPlusUserData $oUserData */
        $oUserData = $this->getNew('paypPayPalPlusUserData');

        $this->_validateBillingPhone($oUserData->getBillingAddressValuePhone())
            ->_validateShippingPhone($oUserData->getShippingAddressValuePhone());

        return true;
    }

    /**
     * Validate phone of shipping address.
     *
     * @param string $sShippingPhone
     *
     * @return self
     * @throws oxException
     */
    protected function _validateShippingPhone($sShippingPhone)
    {
        $blAllowNationalPhoneFormat = !$this->getShop()->getPayPalPlusConfig()->getModuleSetting(
            'PhoneInternationalOnly'
        );
        if (strlen($sShippingPhone) &&
            !$this->_validatePhoneNumber($sShippingPhone, $blAllowNationalPhoneFormat)
        ) {
            if ($blAllowNationalPhoneFormat) {
                throw new oxException('PAYP_PAYPALPLUS_ERROR_SHIPPING_PHONE_1');
            }
            throw new oxException('PAYP_PAYPALPLUS_ERROR_SHIPPING_PHONE');
        }

        return $this;
    }

    /**
     * Validate phone of billing address.
     *
     * @param string $sBillingPhone
     *
     * @return self
     * @throws oxException
     */
    protected function _validateBillingPhone($sBillingPhone)
    {
        $blAllowNationalPhoneFormat = !$this->getShop()->getPayPalPlusConfig()->getModuleSetting(
            'PhoneInternationalOnly'
        );
        if (strlen($sBillingPhone) &&
            !$this->_validatePhoneNumber($sBillingPhone, $blAllowNationalPhoneFormat)
        ) {
            if ($blAllowNationalPhoneFormat) {
                throw new oxException('PAYP_PAYPALPLUS_ERROR_BILLING_PHONE_1');
            }
            throw new oxException('PAYP_PAYPALPLUS_ERROR_BILLING_PHONE');
        }

        return $this;
    }

    /**
     * Validate phone number
     *
     * @param string $sInput                phone number to validate.
     * @param bool   $blAllowNationalFormat National format is allowed
     *
     * @return bool
     */
    protected function _validatePhoneNumber($sInput, $blAllowNationalFormat = true)
    {
        $blReturn = false;
        if (strlen($sInput) < 51 &&
            (
                $this->_isFormatE123International($sInput) ||
                (
                    $blAllowNationalFormat &&
                    $this->_isFormatE123National($sInput)
                )
            )
        ) {
            $blReturn = true;
        }

        return $blReturn;
    }

    /**
     * Test string is in E.123 phone international format. E.g.: +31 42 1123 4567.
     *
     * @param string $sInput normally it should be phone number.
     *
     * @return bool
     */
    protected function _isFormatE123International($sInput)
    {
        return (bool)preg_match('/^\+(?:[0-9] ?){6,49}[0-9]$/', $sInput);
    }

    /**
     * Test string is in E.123 phone national format. E.g.: (042) 1123 4567.
     *
     * @param string $sInput normally it should be phone number
     *
     * @return bool
     */
    protected function _isFormatE123National($sInput)
    {
        return (bool)preg_match('/^(?:\([0-9]{2,5}\))(?: ?[0-9]){5,48}$/', $sInput);
    }

    /**
     * To be a valid payment following conditions must be met:
     * - The ExecutedPayment must be an instance of PayPal\Api\Payment (it may be null if the response body is empty)
     * - the payment execution must have been approved by PayPal
     * - the state of the sale must either be "pending" or "completed"
     *
     * A sale may have a pending state, if the currency of the shop is not supported by PayPal and the merchant must
     * take an action to approve it.
     *
     * @param oxOrder $oOrder
     * @param         $oExecutedPayment
     *
     * @return $this
     * @throws oxException
     */
    protected function _validatePayment(oxOrder $oOrder, $oExecutedPayment)
    {
        $sMessage = $this->getShop()->translate('PAYP_PAYPALPLUS_ERROR_PAYMENT_NOT_VALID');

        if (!$oExecutedPayment instanceof PayPal\Api\Payment) {
            $this->_throwPaymentValidationException($sMessage);
        }

        $sPaymentExecutionState = $oExecutedPayment->getState();
        if (!$this->_isPaymentApprovedByPayPal($sPaymentExecutionState)) {
            $this->_throwPaymentValidationException($sMessage);
        }

        $sTransactionState = $this->_getTransactionState($oOrder, $oExecutedPayment);
        if (!($this->_isSaleCompleted($sTransactionState) || $this->_isSalePending($sTransactionState))) {
            $this->_throwPaymentValidationException($sMessage);
        }

        return $this;
    }

    /**
     * Test if payment execution is approved
     * The state of the transactions of this payment may be different
     *
     * @param string $sPaymentExecutionState
     *
     * @return bool
     */
    protected function _isPaymentApprovedByPayPal($sPaymentExecutionState)
    {
        return $sPaymentExecutionState == $this->getShop()->getPayPalPlusConfig()->getExecutedPaymentSuccessStatus();
    }

    /**
     * Returns true if the transaction state is completed
     *
     * @param $sTransactionState
     *
     * @return bool
     */
    protected function _isSaleCompleted($sTransactionState)
    {
        return $sTransactionState == $this->getShop()->getPayPalPlusConfig()->getRefundablePaymentStatus();
    }

    /**
     * Returns true if the transaction state is pending.
     *
     * @param $sTransactionState
     *
     * @return bool
     */
    protected function _isSalePending($sTransactionState)
    {
        return $sTransactionState == $this->getShop()->getPayPalPlusConfig()->getTransactionPendingState();
    }

    protected function _isSalePaymentUponInvoice($sPaymentInstructionInstructionType)
    {
        return $sPaymentInstructionInstructionType == $this->getShop()->getPayPalPlusConfig()
                ->getPaymentOverInvoiceInstructionType();
    }

    /**
     * Get the state of the transaction
     *
     * @param oxOrder             $oOrder
     * @param \PayPal\Api\Payment $oPayment
     *
     * @return mixed
     */
    protected function _getTransactionState(oxOrder $oOrder, PayPal\Api\Payment $oPayment)
    {
        $oPaymentDataProvider = $this->_getPayPalPlusPaymentDataProvider();
        $oPaymentDataProvider->init($oOrder, $oPayment);
        $aPaymentData      = $oPaymentDataProvider->getData();
        $sTransactionState = $aPaymentData['Status'];

        return $sTransactionState;
    }

    /**
     * Get the InstructionType of the transaction
     *
     * @param oxOrder             $oOrder
     * @param \PayPal\Api\Payment $oPayment
     *
     * @return mixed
     */
    protected function _getPaymentInstructionInstructionType(oxOrder $oOrder, PayPal\Api\Payment $oPayment)
    {
        $oShop = $this->getShop();

        /** @var paypPayPalPlusPuiDataProvider $oDataProvider */
        $oDataProvider = $this->_getPayPalPlusPuiDataProvider();
        $oDataProvider->init($oOrder, $oPayment);
        $sPaymentInstructionInstructionType = $oDataProvider->getPaymentInstructionInstructionType();

        return $sPaymentInstructionInstructionType;
    }

    /**
     * Test helper function.
     *
     * @codeCoverageIgnore
     *
     * @return paypPayPalPlusPuiDataProvider
     */
    protected function _getPayPalPlusPuiDataProvider()
    {
        $oShop = $this->getShop();

        /** @var paypPayPalPlusPuiDataProvider $oDataProvider */
        $oDataProvider = $oShop->getNew('paypPayPalPlusPuiDataProvider');

        return $oDataProvider;
    }

    /**
     * Throws payment validation exception - this is for unit testing
     *
     * @throws oxException
     */
    protected function _throwPaymentValidationException($sMessage)
    {
        /**  @var $oException oxException */
        $oException = oxNew('oxException');
        $oException->setMessage($sMessage);
        throw $oException;
    }

    /**
     * Throws InvalidArgumentException exception - this is for unit testing
     *
     * @codeCoverageIgnore
     *
     * @throws \InvalidArgumentException
     */
    protected function _throwInvalidArgumentException()
    {
        throw new \InvalidArgumentException();
    }

    /**
     * Get instance of paypPayPalPlusPaymentDataProvider
     *
     * Needed for testing.
     *
     * @codeCoverageIgnore
     *
     * @return paypPayPalPlusPaymentDataProvider
     */
    protected function _getPayPalPlusPaymentDataProvider()
    {
        $oShop = $this->getShop();

        $oPaymentDataProvider = $oShop->getNew('paypPayPalPlusPaymentDataProvider');

        return $oPaymentDataProvider;
    }

    /**
     * @param paypPayPalPlusSdk $oSdk
     * @param \PayPal\Api\Payer $oPayer
     */
    protected function addPayerInfo($oSdk, $oPayer)
    {
        /** @var \PayPal\Api\PayerInfo $oPayerInfo */
        $oPayerInfo      = $oSdk->newPayerInfo();
        $oBillingAddress = $oSdk->newAddress();
        $oDataAccess     = $this->getShop()->getDataAccess();
        /** @var paypPayPalPlusUserData $oUserData */
        $oUserData = $this->getShop()->getNew('paypPayPalPlusUserData');
        $oDataAccess->transfuse($oUserData, $oBillingAddress, $oUserData->getFields('Address'), 'BillingAddressValue');
        $oPayerInfo->setBillingAddress($oBillingAddress);
        $oDataAccess->transfuse($oUserData, $oPayerInfo, $oUserData->getFields('PayerInfo'), '');
        $oPayer->setPayerInfo($oPayerInfo);
    }
}
