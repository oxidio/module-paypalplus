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
 * Class paypPayPalPlusDataProvider.
 * Abstract data provider class.
 */
abstract class paypPayPalPlusDataProvider extends paypPayPalPlusSuperCfg
{

    /**
     * Data access utils instance.
     *
     * @var null|paypPayPalPlusDataAccess
     */
    protected $_oDataAccess = null;

    /**
     * Data converter instance.
     *
     * @var null|paypPayPalPlusDataConverter
     */
    protected $_oConverter = null;

    /**
     * Get a list of fields names relevant for SDK objects.
     *
     * @var array
     */
    protected $_aFields = array();

    /**
     * An array of objects to get data from.
     *
     * @var null|array
     */
    protected $_aDataSources = null;

    /**
     * Mapped data array.
     *
     * @var null|array
     */
    protected $_aData = null;


    /**
     * Abstract method to return eShop data mapped for SDK objects with SDK object names as keys and
     * related data arrays as values.
     *
     * @return object
     */
    abstract public function getData();


    /**
     * Abstract method to collect eShop data sources with object names as keys and related objects as values.
     *
     * @return array
     */
    abstract protected function _getSources();


    /**
     * A magic getter method to fetch values from the data provider.
     *
     * Usage examples:
     *   getInstanceName()               -> Takes data array element with key "InstanceName": $aData['InstanceName']
     *   getInstanceNameValueFieldName() -> Takes data array element with key "InstanceName" and then
     *                                      its element with key "FieldName": $aData['InstanceName']['FieldName']
     *
     * @param string $sName
     * @param array  $aArguments
     *
     * @return mixed
     */
    public function __call($sName, $aArguments)
    {
        $mValueLocator = $this->parseCallFor('get', $sName);

        if (is_null($mValueLocator)) {
            return null;
        }

        // Pre-load all data
        if (is_null($this->_aData)) {
            $this->_aData = $this->getData();
        }

        $mReturn = $this->_aData;
        $aValueLocator = explode('Value', $mValueLocator);

        foreach ($aValueLocator as $sKey) {
            $mReturn = $this->_getArrayValue($sKey, $mReturn);
        }

        return $mReturn;
    }


    /**
     * Get data access utils instance.
     *
     * @return paypPayPalPlusDataAccess
     */
    public function getDataUtils()
    {
        if (is_null($this->_oDataAccess)) {
            $this->_oDataAccess = $this->getShop()->getDataAccess();
        }

        return $this->_oDataAccess;
    }

    /**
     * Get data converter instance.
     *
     * @return paypPayPalPlusDataConverter
     */
    public function getConverter()
    {
        if (is_null($this->_oConverter)) {
            $this->_oConverter = $this->getShop()->getConverter();
        }

        return $this->_oConverter;
    }


    /**
     * Get DSK object fields by object name or get all data provider fields.
     *
     * @param string $sObjectName
     *
     * @return null|array
     */
    public function getFields($sObjectName = '')
    {
        if (!empty($sObjectName)) {
            return $this->getDataUtils()->getArrayValue((string) $sObjectName, $this->_aFields);
        }

        return $this->_aFields;
    }


    /**
     * Get data source object by class name.
     *
     * @param string $sClassName
     *
     * @return null|object
     */
    protected function _getSourceObject($sClassName)
    {
        $mReturn = null;

        if (is_null($this->_aDataSources)) {
            $this->_aDataSources = $this->_getSources();
        }

        $oObject = $this->getDataUtils()->getArrayValue((string) $sClassName, $this->_aDataSources);

        if (is_object($oObject) and ($oObject instanceof $sClassName)) {
            $mReturn = $oObject;
        }

        return $mReturn;
    }

    /**
     * Get array value if data is an array and key is valid and existing.
     *
     * @param string      $sKey
     * @param array|mixed $mData
     *
     * @return mixed|null
     */
    protected function _getArrayValue($sKey, $mData)
    {
        if (!empty($sKey) and is_scalar($sKey) and is_array($mData)) {
            $mData = $this->getDataUtils()->getArrayValue($sKey, $mData);
        }

        return $mData;
    }
}
