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

// Load SDK
require_once dirname(__FILE__) . '/../vendor/autoload.php';


/**
 * Class paypPayPalPlusSession
 * PayPal credentials token, API context and payment data session handler.
 */
class paypPayPalPlusSession extends paypPayPalPlusSuperCfg
{

    /**
     * A session key for basket hash storage
     *
     * @var string
     */
    protected $_sBasketHashSessionKey = 'paypPayPalPlusBasketHash';

    /**
     * A session key for API Context object storage.
     *
     * @var string
     */
    protected $_sApiContextSessionKey = 'paypPayPalPlusApiContext';

    /**
     * A session key for Payment object storage.
     *
     * @var string
     */
    protected $_sPaymentSessionKey = 'paypPayPalPlusPayment';

    /**
     * A session key for Approved Payment object storage.
     *
     * @var string
     */
    protected $_sApprovedPaymentSessionKey = 'paypPayPalPlusApprovedPayment';

    /**
     * A session key for Payment ID value storage.
     *
     * @var string
     */
    protected $_sPaymentIdSessionKey = 'paypPayPalPlusPaymentId';

    /**
     * A session key for Approved Payment ID value storage.
     *
     * @var string
     */
    protected $_sApprovedPaymentIdSessionKey = 'paypPayPalPlusApprovedPaymentId';

    /**
     * A session key for Payer ID value storage.
     *
     * @var string
     */
    protected $_sPayerIdSessionKey = 'paypPayPalPlusPayerId';

    /**
     * A session key for the invoice number set in the transaction.
     * This is the oxOrder::OIXD
     *
     * @var string
     */
    protected $_sInvoiceNumberSessionKey = 'paypPayPalPlusInvoiceNumber';


    /**
     * Save basket stamp to session - a hash key based on serialized basket object.
     *
     * @param string $sHash
     */
    public function setBasketStamp($sHash)
    {
        $this->_set($this->_sBasketHashSessionKey, $sHash);
    }

    /**
     * Get basket stamp from session - a hash key based on serialized basket object.
     *
     * @return string
     */
    public function getBasketStamp()
    {
        return $this->_get($this->_sBasketHashSessionKey);
    }

    /**
     * Serialize API Context instance and save to session.
     *
     * @param PayPal\Rest\ApiContext $oApiContext
     */
    public function setApiContext($oApiContext)
    {
        $this->_set($this->_sApiContextSessionKey, $oApiContext);
    }

    /**
     * Get un-serialized API Context instance from session.
     *
     * @return bool|PayPal\Rest\ApiContext
     */
    public function getApiContext()
    {
        return $this->_get($this->_sApiContextSessionKey);
    }

    /**
     * Serialize Payment instance and save to session.
     *
     * @param PayPal\Api\Payment $oPayment
     */
    public function setPayment($oPayment)
    {
        $this->_set($this->_sPaymentSessionKey, $oPayment);

        if ($sId = $oPayment->getId()) {
            $this->getShop()->setSessionVariable($this->_sPaymentIdSessionKey, $sId);
        }
    }

    /**
     * Serialize Approved Payment instance and save to session.
     *
     * @param PayPal\Api\Payment $oPayment
     */
    public function setApprovedPayment($oPayment)
    {
        $this->_set($this->_sApprovedPaymentSessionKey, $oPayment);

        if ($sId = $oPayment->getId()) {
            $this->getShop()->setSessionVariable($this->_sApprovedPaymentIdSessionKey, $sId);
        }
    }

    /**
     * Unset Approved Payment from session.
     */
    public function unsetApprovedPayment()
    {
        $oShop = $this->getShop();

        $oShop->deleteSessionVariable($this->_sApprovedPaymentSessionKey);
        $oShop->deleteSessionVariable($this->_sApprovedPaymentIdSessionKey);
    }

    /**
     * Get un-serialized Payment instance from session.
     *
     * @return bool|PayPal\Api\Payment
     */
    public function getPayment()
    {
        return $this->_get($this->_sPaymentSessionKey);
    }

    /**
     * Get un-serialized Approved Payment instance from session.
     *
     * @return bool|PayPal\Api\Payment
     */
    public function getApprovedPayment()
    {
        return $this->_get($this->_sApprovedPaymentSessionKey);
    }

    /**
     * Get payment ID value from session.
     *
     * @return string
     */
    public function getPaymentId()
    {
        return (string) $this->getShop()->getSessionVariable($this->_sPaymentIdSessionKey);
    }

    /**
     * Get approved payment ID value from session.
     *
     * @return string
     */
    public function getApprovedPaymentId()
    {
        return (string) $this->getShop()->getSessionVariable($this->_sApprovedPaymentIdSessionKey);
    }

    /**
     * Set payer ID value to session.
     *
     * @param $sPayerId
     */
    public function setPayerId($sPayerId)
    {
        $this->getShop()->setSessionVariable($this->_sPayerIdSessionKey, $sPayerId);
    }

    /**
     * Get payer ID value from session.
     *
     * @return mixed
     */
    public function getPayerId()
    {
        return $this->getShop()->getSessionVariable($this->_sPayerIdSessionKey);
    }

    /**
     * Store the invoice number in the session.
     *
     * @param $sInvoiceNumber
     */
    public function setInvoiceNumber ($sInvoiceNumber) {
        $this->getShop()->setSessionVariable($this->_sInvoiceNumberSessionKey, $sInvoiceNumber);
    }

    /**
     * Retrieve the invoicenumber from the session.
     *
     * @return mixed
     */
    public function getInvoiceNumber () {
        return $this->getShop()->getSessionVariable($this->_sInvoiceNumberSessionKey);
    }

    /**
     * Unset all PayPal Plus session keys.
     */
    public function reset()
    {
        $oShop = $this->getShop();

        $oShop->deleteSessionVariable($this->_sBasketHashSessionKey);
        $oShop->deleteSessionVariable($this->_sApiContextSessionKey);
        $oShop->deleteSessionVariable($this->_sPaymentSessionKey);
        $oShop->deleteSessionVariable($this->_sApprovedPaymentSessionKey);
        $oShop->deleteSessionVariable($this->_sPaymentIdSessionKey);
        $oShop->deleteSessionVariable($this->_sApprovedPaymentIdSessionKey);
        $oShop->deleteSessionVariable($this->_sPayerIdSessionKey);
        $oShop->deleteSessionVariable($this->_sInvoiceNumberSessionKey);
    }


    /**
     * Initialise PayPal session.
     * It creates or updates credentials token and save new API context instance to session.
     */
    public function init()
    {
        $oSdk = $this->getSdk();

        // Create new credentials token
        $oToken = $oSdk->newTokenCredential();

        try {
            $oToken->getAccessToken($oSdk->getSdkConfig());
        } catch (Exception $oException) {
            // Fail silently - because not yet sure if user would user PayPal method.
        }

        // Create new API context
        $oApiContext = $oSdk->newApiContext($oToken);

        // Save the API Context to session
        $this->setApiContext($oApiContext);
    }


    /**
     * Serialize object and save it to session.
     *
     * @param string $sSessionKey
     * @param object $oObject
     */
    protected function _set($sSessionKey, $oObject)
    {
        $this->getShop()->setSessionVariable($sSessionKey, serialize($oObject));
    }

    /**
     * Get value from session and un-serialize it as object.
     *
     * @param string $sSessionKey
     *
     * @return bool|object
     */
    protected function _get($sSessionKey)
    {
        return unserialize($this->getShop()->getSessionVariable($sSessionKey));
    }
}
