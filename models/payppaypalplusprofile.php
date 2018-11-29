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
 * Class paypPayPalPlusProfile.
 * User profile related objects handler.
 */
class paypPayPalPlusProfile extends paypPayPalPlusSuperCfg
{

    /**
     * Check if the user or address is a part of the currently logged in user profile.
     * Trigger post save procedures on a positive result.
     *
     * @param oxUser|oxAddress|oxBase $oObject
     */
    public function postSave(oxBase $oObject)
    {
        if ($oObject instanceof oxUser) {
            $this->_checkIfCurrentUserAndPostSave($oObject);
        } elseif ($oObject instanceof oxAddress) {
            $this->_checkIfAddressBelongsToCurrentUserAndPostSave($oObject);
        }
    }


    /**
     * Check if given user object is the same as current logged in user.
     * Trigger post save procedures on a positive result.
     *
     * @param oxUser $oUser
     */
    protected function _checkIfCurrentUserAndPostSave(oxUser $oUser)
    {
        $oCurrentUser = $this->getShop()->getUser();

        if (($oCurrentUser instanceof oxUser) and ($oCurrentUser->getId() === $oUser->getId())) {
            $this->_postSave();
        }
    }

    /**
     * Check if given address object the active shipping addresses of the current logged in user.
     * Trigger post save procedures on a positive result.
     *
     * @param oxAddress $oAddress
     */
    protected function _checkIfAddressBelongsToCurrentUserAndPostSave(oxAddress $oAddress)
    {
        $oShop = $this->getShop();
        $oCurrentUser = $oShop->getUser();

        if (($oCurrentUser instanceof oxUser) and
            $oShop->getSessionVariable('blshowshipaddress') and
            $this->_isItUserActiveShippingAddress($oCurrentUser, $oAddress)
        ) {
            $this->_postSave();
        }
    }

    /**
     * Check if an address matches current user active shipping address.
     *
     * @param oxUser    $oCurrentUser
     * @param oxAddress $oAddress
     *
     * @return bool
     */
    protected function _isItUserActiveShippingAddress(oxUser $oCurrentUser, oxAddress $oAddress)
    {
        $oShippingAddress = $oCurrentUser->getSelectedAddress();

        return (($oShippingAddress instanceof oxAddress) and ($oShippingAddress->getId() === $oAddress->getId()));
    }

    /**
     * User profile post save hook.
     * Triggers on current active user changes of their selected shipping address changes.
     * Completely resets PayPal Plus payment session.
     */
    protected function _postSave()
    {
        $this->getShop()->getPayPalPlusSession()->reset();
    }
}
