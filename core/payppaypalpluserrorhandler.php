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
 * Class paypPayPalPlusErrorHandler.
 * Error handling helper: process exceptions, set and trigger errors display.
 */
class paypPayPalPlusErrorHandler extends paypPayPalPlusSuperCfg
{

    /**
     * Error code for general (not parsed) PayPal Plus API errors.
     *
     * @var string
     */
    protected $_sGeneralErrorCode = '_PAYP_PAYPALPLUS_ERROR_';

    /**
     * PayPal API error codes to be parsed.
     *
     * @var array
     */
    protected $_aErrorsToParse = array(
        'ADDRESS_INVALID',
        'VALIDATION_ERROR',
        'PAYP_PAYPALPLUS_ERR_INVOICE_UPDATE_REQUIRED',
        'PAYP_PAYPALPLUS_ERR_INVOICE_EXISTS',
    );

    /**
     * A session key to store payment error code in.
     *
     * @var string
     */
    protected $_sPaymentErrorSessionKey = 'payerror';

    /**
     * A name of checkout payment step controller.
     *
     * @var string
     */
    protected $_sPaymentControllerName = 'payment';


    /**
     * Get an error code for general (not parsed) PayPal Plus API errors.
     *
     * @return string
     */
    public function getGeneralErrorCode()
    {
        return $this->_sGeneralErrorCode;
    }

    /**
     * Get a session key that stores payment error code.
     *
     * @return string
     */
    public function getPaymentErrorKey()
    {
        return $this->_sPaymentErrorSessionKey;
    }


    /**
     * Set data validation notice to display.
     * @TODO use handelValidationMessage instead and adjust tests.
     */
    public function setDataValidationNotice()
    {
        $this->handelValidationMessage('PAYP_PAYPALPLUS_ERROR_ADDRESS');
    }

    /**
     * Handle validation messages
     *
     * @param string $sMessageLangKey Message lang key.
     */
    public function handelValidationMessage( $sMessageLangKey )
    {
        $oShop = $this->getShop();

        $oException = $oShop->getNew('oxExceptionToDisplay');
        $oException->setMessage($sMessageLangKey);

        $oViewUtils = $oShop->getFromRegistry("oxUtilsView");
        $oViewUtils->addErrorToDisplay($oException, false);
    }

    /**
     * Set error to display and redirect user to checkout payment step.
     *
     * @param int $iErrorCode One of eShop payment error codes.
     */
    public function setPaymentErrorAndRedirect($iErrorCode = 2)
    {
        $oShop = $this->getShop();
        $oShop->setSessionVariable($this->getPaymentErrorKey(), $iErrorCode);

        $sRedirectUrl = $oShop->getConfig()->getShopCurrentUrl() . '&cl=' . $this->_sPaymentControllerName;
        $oShop->getUtils()->redirect($sRedirectUrl);
    }

    /**
     * Parse an exception for known error codes and exit with or return an error message.
     * If exception is not parsed, exits with or returns a default error code.
     *
     * @param PayPal\Exception\PayPalConnectionException|Exception $oException
     * @param bool $blOnlyReturnOutput Return an error as string if True,
     *                                                                            exit with error if False (default)
     *
     * @return null|string
     */
    public function parseError(Exception $oException, $blOnlyReturnOutput = false)
    {
        $sMessage = $this->getGeneralErrorCode();

        if ($oException instanceof PayPal\Exception\PayPalConnectionException) {
            list ($sErrorCode, $sErrorMessage) = $this->_parsePayPalApiError($oException);

            if (in_array($sErrorCode, $this->_aErrorsToParse) and !empty($sErrorMessage)) {
                $sMessage = $sErrorMessage;
            }
        }

        if ($oException instanceof paypPayPalPlusRefundException) {
            /** Print Exception to EXCEPTION_LOG.txt */
            $oException->debugOut();

            $sErrorMessage = $oException->getMessage();
            if (in_array($sErrorMessage, $this->_aErrorsToParse)) {
                $sMessage =  $this->getShop()->translate((string) $sErrorMessage, true);
            }
        }

        if ($oException instanceof oxException) {
            $oException->debugOut();
        }

        return $this->_exitWithError($sMessage, $blOnlyReturnOutput);
    }

    /**
     * If module is in debug mode, output the exception.
     *
     * @param PayPal\Exception\PayPalConnectionException|Exception $oException
     * @param null|PayPal\Common\PayPalModel                       $mPayPalApiModel
     *
     * @codeCoverageIgnore
     */
    public function debug(Exception $oException, $mPayPalApiModel = null)
    {
        if (!defined('OXID_PHP_UNIT') and $this->getShop()->getPayPalPlusConfig()->getModuleSetting('Debug')) {
            print('<pre>');

            if ($mPayPalApiModel instanceof PayPal\Common\PayPalModel) {
                print_r($mPayPalApiModel);
            }

            print_r($oException);
        }
    }


    /**
     * Parse PayPal API exception for human-readable error message and error code.
     * Additionally tries to parse and add error details.
     *
     * @param \PayPal\Exception\PayPalConnectionException $oException
     *
     * @return array Contains error code and as first element and error text as second one.
     */
    protected function _parsePayPalApiError(PayPal\Exception\PayPalConnectionException $oException)
    {
        $oDataUtils = $this->getShop()->getDataAccess();

        $aErrorData = (array) json_decode($oException->getData());
        $sErrorCode = (string) $oDataUtils->getArrayValue('name', $aErrorData);
        $sErrorText = (string) $oDataUtils->getArrayValue('message', $aErrorData);
        $aErrorDetails = (array) $oDataUtils->getArrayValue('details', $aErrorData);

        foreach ($aErrorDetails as $oDetails) {
            $sErrorText .= ': (' . (string) $oDataUtils->invokeGet($oDetails, 'field:$') . ') ' .
                           (string) $oDataUtils->invokeGet($oDetails, 'issue:$');
        }

        $sErrorText = trim($sErrorText);

        return array($sErrorCode, $sErrorText);
    }

    /**
     * Simply returns an error or acts as an alias for `exit` function to print the error and exit.
     *
     * @codeCoverageIgnore
     * @param string $sMessage
     * @param bool $blOnlyReturnOutput
     *
     * @return string
     */
    protected function _exitWithError($sMessage, $blOnlyReturnOutput)
    {

        if (!empty($blOnlyReturnOutput)) {
            return $sMessage;
        }

        return exit($sMessage);
    }
}
