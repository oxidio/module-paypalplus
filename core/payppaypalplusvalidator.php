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
 * Class paypPayPalPlusValidator.
 * Provides a set of methods to check module status, basket and user states, PayPal API state.
 */
class paypPayPalPlusValidator extends paypPayPalPlusSuperCfg
{

    /**
     * Check if PayPal payment instance could be created.
     * Only possible if the module and PayPal Plus payment are active, basket payable and PayPal API available.
     *
     * @param bool $blSkipApiCheck If True API connectivity is not checked, if False it also checks API auth token.
     *
     * @return bool
     */
    public function isPaymentPossible($blSkipApiCheck = true)
    {
        return ($this->_isPaymentPossibleInShop() and ($blSkipApiCheck or $this->isApiAvailable()));
    }

    /**
     * Check if PayPal Plus module is active and registered within eShop.
     *
     * @return bool
     */
    public function isModuleActive()
    {
        $oModule = $this->getShop()->getPayPalPlusModule();

        return ($oModule->isActive() and $oModule->isRegistered());
    }

    /**
     * Check if PayPal Plus payment method is available and active.
     *
     * @return bool
     */
    public function isPaymentActive()
    {
        $blActive = false;

        /** @var oxPayment $oPayment */
        $oPayment = $this->getNew('oxPayment');

        if ($oPayment->load($this->getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId())) {
            $blActive = ($oPayment->getId() and !empty($oPayment->oxpayments__oxactive->value));
        }

        return $blActive;
    }

    /**
     * Check if basket has products and total amount is more than zero.
     *
     * @return bool
     */
    public function isBasketPayable()
    {
        $blPayable = false;

        $oBasket = $this->getShop()->getBasket();

        $iProductsCount = (int) $oBasket->getProductsCount();

        if ($iProductsCount > 0) {
            $oPrice = $oBasket->getPrice();

            if ($oPrice instanceof oxPrice) {
                $dPrice = (double) $oPrice->getPrice();
                $blPayable = ($dPrice > 0.0);
            }
        }

        return $blPayable;
    }

    /**
     * Check if PayPal API is available.
     * It should be able to get authentication token.
     *
     * @todo (nice2have): Implement credentials exception handling here, or remove the method if never used.
     *
     * @return bool
     */
    public function isApiAvailable()
    {
        $blAvailable = false;
        $oApiContext = $this->getShop()->getPayPalPlusSession()->getApiContext();

        if ($oApiContext instanceof PayPal\Rest\ApiContext) {
            $oToken = $oApiContext->getCredential();

            if ($oToken instanceof PayPal\Auth\OAuthTokenCredential) {

                try {
                    $sToken = $oToken->getAccessToken($this->getSdk()->getSdkConfig());
                    $blAvailable = !empty($sToken);
                } catch (Exception $oException) {
                    // Fail silently
                }
            }
        }

        return $blAvailable;
    }

    /**
     * Check if PayPal Payment instance is available in session and was created within API.
     *
     * @param null|PayPal\Api\Payment $oPayment
     *
     * @return bool
     */
    public function isPaymentCreated($oPayment = null)
    {
        $blCreated = false;

        if (is_null($oPayment)) {
            $mPayment = $this->getShop()->getPayPalPlusSession()->getPayment();
        } else {
            $mPayment = $oPayment;
        }

        if ($mPayment instanceof PayPal\Api\Payment) {
            $sId = $mPayment->getId();
            $blCreated = !empty($sId);
        }

        return $blCreated;
    }


    /**
     * Check eShop conditions for PayPal Plus payment to be possible.
     * PayPal Plus module and payment method should be both active and basket should be payable.
     *
     * @return bool
     */
    protected function _isPaymentPossibleInShop()
    {
        return ($this->isModuleActive() and $this->isPaymentActive() and $this->isBasketPayable());
    }
}
