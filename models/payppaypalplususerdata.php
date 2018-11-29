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
 * @link          http://www.paypal.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

/**
 * Class paypPayPalPlusUserData.
 * User account and addresses data provider for SDK Address.
 */
class paypPayPalPlusUserData extends paypPayPalPlusDataProvider
{

    /**
     * User profile and address fields names for SDK object Address.
     *
     * @var array
     */
    protected $_aFields = array(
        'Address'   => array('RecipientName', 'Line1', 'Line2', 'City', 'CountryCode', 'State', 'PostalCode', 'Phone'),
        'PayerInfo' => array('FirstName', 'LastName', 'Email'),
    );

    /**
     * Get user data mapped for SDK objects.
     * It maps billing and shipping addresses separately.
     *
     * @return array
     */
    public function getData()
    {
        /** @var oxUser $oUser */
        $oUser  = $this->_getSourceObject('oxUser');
        $oUtils = $this->getDataUtils();
        $oCast  = $this->getConverter();


        // Billing address data
        $aBillingAddress = $this->_getBillingAddressData($oUser);

        // Shipping address data
        $blSeparateShippingAddress = (bool)$this->getShop()->getSessionVariable('blshowshipaddress');

        if (empty($blSeparateShippingAddress)) {
            $aShippingAddress = $aBillingAddress;
        } else {
            $aShippingAddress = $this->_getShippingAddressData($oUser);
        }

        // All user data
        $aData = array(
            'BillingAddress'  => $aBillingAddress,
            'ShippingAddress' => $aShippingAddress,
            'FirstName'       => $oCast->string($oUtils->invokeGet($oUser, 'oxuser__oxfname:$', 'value:$')),
            'LastName'        => $oCast->string($oUtils->invokeGet($oUser, 'oxuser__oxlname:$', 'value:$')),
            'Email'           => $oCast->string($oUtils->invokeGet($oUser, 'oxuser__oxusername:$', 'value:$')),
        );

        return $aData;
    }

    /**
     * Get active shop user object, new country and state objects.
     *
     * @return array
     */
    protected function _getSources()
    {
        return array(
            'oxUser'    => $this->getShop()->getUser(),
            'oxCountry' => $this->getNew('oxCountry'),
            'oxState'   => $this->getNew('oxState'),
        );
    }

    /**
     * Get user billing address data.
     *
     * @param oxUser $oUser
     *
     * @return array
     */
    protected function _getBillingAddressData(oxUser $oUser)
    {
        return $this->_getAddressData($oUser, 'oxuser');
    }

    /**
     * Get user shipping address data
     *
     * @param oxUser $oUser
     *
     * @return array
     */
    protected function _getShippingAddressData(oxUser $oUser)
    {
        /** @var oxAddress $oAddress */
        $oAddress = $this->getDataUtils()->invokeGet($oUser, 'getSelectedAddress');

        return $this->_getAddressData($oAddress, 'oxaddress');
    }

    /**
     * Get address data from an object.
     * The object should be either user or address object to match address fields.
     *
     * @param oxUser|oxAddress $oObject
     * @param string           $sTableName
     *
     * @return array
     */
    protected function _getAddressData($oObject, $sTableName)
    {
        $oUtils = $this->getDataUtils();
        $oCast  = $this->getConverter();

        /** @var oxCountry $oCountry */
        $oCountry = $this->_getSourceObject('oxCountry');
        $oCountry->load($oUtils->invokeGet($oObject, $sTableName . '__oxcountryid:$', 'value:$'));

        /** @var oxState $oState */
        $oState = $this->_getSourceObject('oxState');
        $oState->load($oUtils->invokeGet($oObject, $sTableName . '__oxstateid:$', 'value:$'));

        $aFullName  = array();
        $sLastName  = $oUtils->invokeGet($oObject, $sTableName . '__oxlname:$', 'value:$');
        $sFirstName = $oUtils->invokeGet($oObject, $sTableName . '__oxfname:$', 'value:$');

        if (!empty($sFirstName)) {
            $aFullName[] = $sFirstName;
        }

        if (!empty($sLastName)) {
            $aFullName[] = $sLastName;
        }

        return array(
            'RecipientName' => $oCast->string(implode(' ', $aFullName), 50),
            'Line1'         => $oCast->string(
                $oUtils->invokeGet($oObject, $sTableName . '__oxstreet:$', 'value:$') .
                ' ' .
                $oUtils->invokeGet($oObject, $sTableName . '__oxstreetnr:$', 'value:$')
            ),
            'Line2'         => $oCast->string($oUtils->invokeGet($oObject, $sTableName . '__oxaddinfo:$', 'value:$')),
            'City'          => $oCast->string($oUtils->invokeGet($oObject, $sTableName . '__oxcity:$', 'value:$'), 40),
            'CountryCode'   => $oCast->string($oUtils->invokeGet($oCountry, 'oxcountry__oxisoalpha2:$', 'value:$'), 2),
            'State'         => $oCast->string($oUtils->invokeGet($oState, 'oxstates__oxisoalpha2:$', 'value:$')),
            'PostalCode'    => $oCast->string($oUtils->invokeGet($oObject, $sTableName . '__oxzip:$', 'value:$'), 20),
            // See PSPPP-189 no-phone-number-will-be-passed
            'Phone'         => ''
            // $oCast->string($oUtils->invokeGet($oObject, $sTableName . '__oxfon:$', 'value:$'), 50),
        );
    }
}
