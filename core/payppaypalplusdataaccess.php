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
 * Class paypPayPalPlusDataAccess.
 * Array and object data access utils.
 */
class paypPayPalPlusDataAccess extends paypPayPalPlusSuperCfg
{
    /**
     * @var PayPal\Core\PayPalLoggingManager|null
     */
    protected $_logger = null;

    /**
     * Get array value by key.
     *
     * @param mixed $mKey
     * @param array $aData
     *
     * @return null|mixed
     */
    public function getArrayValue($mKey, array $aData)
    {
        $mValue = null;

        if (array_key_exists($mKey, $aData)) {
            $mValue = $aData[$mKey];
        }

        return $mValue;
    }

    /**
     * Get data from object if it is valid.
     * Could have multiple parameters for a chain of getters.
     * Each param is a getter method or property to call with optional arguments.
     *
     * Usage examples:
     *  invokeGet($oBasket, 'getPrice', 'getPrice');            -> $oBasket->getPrice()->getPrice();
     *  invokeGet($oBasket, 'getCosts:oxdelivery', 'getPrice'); -> $oBasket->getCosts('oxdelivery')->getPrice()
     *  invokeGet($oUser, 'oxuser__oxfon:$', 'value:$');        -> $oUser->oxuser__oxfon->value
     *  invokeGet($aData, '0:[]', 'name:[]');                   -> $aData[0]['name']
     *
     * @param object $oObject
     *
     * @return null|mixed
     */
    public function invokeGet($oObject)
    {
        $mValue = $oObject;

        $aInvokeArguments = (array) func_get_args();
        unset($aInvokeArguments[0]);

        foreach ($aInvokeArguments as $sInvokeArgument) {
            if (!is_object($mValue) and !is_array($mValue)) {
                $mValue = null;
                break;
            }

            $mValue = $this->_invokeByArgument($mValue, $sInvokeArgument);
        }

        return $mValue;
    }

    /**
     * Get fields values from a source object (instance of data provider) and
     * set to a target object (SDK API model or OXID eShop model).
     * Sets only non-empty value.
     *
     * @param paypPayPalPlusDataProvider       $oSource
     * @param PayPal\Common\PayPalModel|oxBase $oTarget
     * @param array                            $aFields
     * @param string                           $sGetterPrefix
     */
    public function transfuse(paypPayPalPlusDataProvider $oSource, $oTarget, array $aFields, $sGetterPrefix = '')
    {
        if (($oTarget instanceof PayPal\Common\PayPalModel) or ($oTarget instanceof oxBase)) {
            foreach ($aFields as $sField) {
                $this->_assignFieldCrossObjects($sField, $sGetterPrefix, $oSource, $oTarget);
            }
        }
    }


    /**
     * Parse invoke argument and call appropriate getter method to return a value.
     *
     * @param object|array $mValue
     * @param string       $sInvokeArgument
     *
     * @return mixed
     */
    protected function _invokeByArgument($mValue, $sInvokeArgument)
    {
        $mReturn = null;
        $aInvokeScenariosMap = array(
            'method'          => '_callObjectMethod',
            'array_value'     => '_getArrayValue',
            'property'        => '_getObjectProperty',
            'method_argument' => '_callObjectMethodWithArgument',
        );

        list($mKey, $mProperty, $mMethod, $mArgument, $sInvokeScenario) = $this->_parseInvokeArgument($sInvokeArgument);
        $sMethod = $this->getArrayValue($sInvokeScenario, $aInvokeScenariosMap);

        if (!empty($sMethod) and method_exists($this, $sMethod)) {
            $mReturn = $this->$sMethod($mValue, $mKey, $mProperty, $mMethod, $mArgument);
        }

        return $mReturn;
    }

    /**
     * Parse invoke argument to array, property or method with optional argument.
     *
     * Usage examples:
     *  ...->_parseInvokeArgument('getSomething')       -> array(null, null, getSomething, null, 'method')
     *  ...->_parseInvokeArgument('object_property:$')  -> array(null, 'object_property', null, null, 'property')
     *  ...->_parseInvokeArgument('getSomething:good')  -> array(null, null, 'getSomething', 'good', 'method_argument')
     *  ...->_parseInvokeArgument('array_key:[]')       -> array('array_key', null, null, null, 'array_value')
     *
     * @param string $sInvokeArgument
     *
     * @return array
     */
    protected function _parseInvokeArgument($sInvokeArgument)
    {
        $aParams = (array) explode(':', $sInvokeArgument);
        $sSubject = (string) $this->getArrayValue(0, $aParams);
        $sModifier = (string) $this->getArrayValue(1, $aParams);

        $mKey = $sProperty = $sArgument = null;
        $sMethod = $sSubject;
        $sInvokeScenario = 'method';

        if ($sModifier === '[]') {
            $mKey = $sMethod;
            $sMethod = null;
            $sInvokeScenario = 'array_value';
        } elseif ($sModifier === '$') {
            $sProperty = $sMethod;
            $sMethod = null;
            $sInvokeScenario = 'property';
        } elseif (!empty($sModifier)) {
            $sArgument = $sModifier;
            $sInvokeScenario = 'method_argument';
        }

        return array($mKey, $sProperty, $sMethod, $sArgument, $sInvokeScenario);
    }

    /**
     * Call object method if exists and return the result.
     *
     * @param array|object $mValue
     * @param null|string  $mKey
     * @param null|string  $mProperty
     * @param null|string  $mMethod
     * @param null|string  $mArgument
     *
     * @return null|mixed
     */
    protected function _callObjectMethod($mValue, $mKey, $mProperty, $mMethod, $mArgument)
    {
        $mReturn = null;

        if (is_object($mValue) and method_exists($mValue, $mMethod)) {
            $mReturn = $mValue->$mMethod();
        }

        return $mReturn;
    }

    /**
     * Get array value by key if available and return it.
     *
     * @param array|object $mValue
     * @param null|string  $mKey
     * @param null|string  $mProperty
     * @param null|string  $mMethod
     * @param null|string  $mArgument
     *
     * @return null|mixed
     */
    protected function _getArrayValue($mValue, $mKey, $mProperty, $mMethod, $mArgument)
    {
        $mReturn = null;

        if (is_array($mValue) and !is_null($mKey)) {
            $mReturn = $this->getArrayValue($mKey, $mValue);
        }

        return $mReturn;
    }

    /**
     * Get object property value if exists and return the result.
     *
     * @param array|object $mValue
     * @param null|string  $mKey
     * @param null|string  $mProperty
     * @param null|string  $mMethod
     * @param null|string  $mArgument
     *
     * @return null|mixed
     */
    protected function _getObjectProperty($mValue, $mKey, $mProperty, $mMethod, $mArgument)
    {
        $mReturn = null;

        if (is_object($mValue)) {
            try {
                $mReturn = $mValue->$mProperty;
            } catch (oxException $oException) {
            }
        }

        return $mReturn;
    }

    /**
     * Call object method if exists using an argument and return the result.
     *
     * @param array|object $mValue
     * @param null|string  $mKey
     * @param null|string  $mProperty
     * @param null|string  $mMethod
     * @param null|string  $mArgument
     *
     * @return null|mixed
     */
    protected function _callObjectMethodWithArgument($mValue, $mKey, $mProperty, $mMethod, $mArgument)
    {
        $mReturn = null;

        if (is_object($mValue) and method_exists($mValue, $mMethod) and !is_null($mArgument)) {
            $mReturn = $mValue->$mMethod($mArgument);
        }

        return $mReturn;
    }

    /**
     * Assign field value from source object to target object if the value is not empty.
     *
     * @param string                           $sField
     * @param string                           $sGetterPrefix
     * @param paypPayPalPlusDataProvider       $oSource
     * @param PayPal\Common\PayPalModel|oxBase $oTarget
     */
    protected function _assignFieldCrossObjects($sField, $sGetterPrefix, paypPayPalPlusDataProvider $oSource, $oTarget)
    {
        $sGetterName = 'get' . $sGetterPrefix . $sField;
        $sSetterName = 'set' . $sField;

        if (method_exists($oTarget, $sSetterName)) {
            $sValue = $oSource->$sGetterName();

            if (!empty($sValue)) {
                $oTarget->$sSetterName($sValue);
            }
        }
    }

    /**
     * Gets the latest delivery date of all basket items.
     *
     * @return string
     */
    public function getBasketDeliveryDate()
    {
        $oBasket = $this->getSession()->getBasket();

        $deliveryDate = date('Y-m-d');

        /** @var oxBasketItem $item */
        foreach ($oBasket->getBasketArticles() as $oArticle)
        {
            $itemAvailabilityDate = $oArticle->oxarticles__oxdelivery->value;

            switch ($oArticle->oxarticles__oxdeltimeunit->value) {
                case 'DAY':
                    $date = new DateTime();
                    $date->add(new DateInterval('P' . $oArticle->oxarticles__oxmaxdeltime->value . 'D'));
                    $itemDeliveryDate = $date->format('Y-m-d');
                    break;
                case 'WEEK':
                    $date = new DateTime();
                    $date->add(new DateInterval('P' . $oArticle->oxarticles__oxmaxdeltime->value . 'W'));
                    $itemDeliveryDate = $date->format('Y-m-d');
                    break;
                case 'MONTH':
                    $date = new DateTime();
                    $date->add(new DateInterval('P' . $oArticle->oxarticles__oxmaxdeltime->value . 'M'));
                    $itemDeliveryDate = $date->format('Y-m-d');
                    break;
                default:
                    $itemDeliveryDate = date('Y-m-d');
                    $this->getLogger()->error('delivery time unit ' . $oArticle->oxarticles__oxdeltimeunit->value . ' not known');
            }

            $deliveryDate = max($deliveryDate, $itemDeliveryDate, $itemAvailabilityDate);
        }

        return $deliveryDate;
    }

    /**
     * Gets the logger form the PayPal sdk.
     *
     * @return \PayPal\Core\PayPalLoggingManager
     */
    protected function getLogger()
    {
        if (!$this->_logger) {
            $this->_logger = PayPal\Core\PayPalLoggingManager::getInstance(__CLASS__);
        }

        return $this->_logger;
    }

}
