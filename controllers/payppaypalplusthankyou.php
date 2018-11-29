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
 * Class paypPayPalPlusThankyou
 *
 * Override the thankyou controller to provide a template getter to display PuI specific content.
 */
class paypPayPalPlusThankyou extends paypPayPalPlusThankyou_parent
{

    /**
     * Payment Instructions to be displayed in the template in case the payment was made with PayPal upon Invoice.
     *
     * @var paypPayPalPlusPuiData
     */
    protected $_oPaymentInstructions;

    /**
     * @var oxShop
     */
    protected $_oShop;

    /**
     * Property getter.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    public function getShop()
    {
        return $this->_oShop;
    }

    /**
     * Property setter.
     *
     * @codeCoverageIgnore
     *
     * @param mixed $oShop
     */
    public function setShop($oShop)
    {
        $this->_oShop = $oShop;
    }

    /**
     * @inheritdoc
     *
     * Additionally to the parent function, this sets the classes _shop object
     */
    public function init()
    {
        $this->setShop(paypPayPalPlusShop::getShop());

        $this->_paypPayPalPlusPayment_init_parent();
    }

    /**
     * Set the Payment Instructions before rendering the page.
     *
     * @inheritdoc
     *
     * @return string
     */
    public function render()
    {
        $this->_setPaymentInstructions();

        return $this->_paypPayPalPlusPayment_render_parent();
    }

    /**
     * Template getter.
     * Get the payment instructions to be displayed in the thankyou page.
     *
     * @return paypPayPalPlusPuiData
     */
    public function getPaymentInstructions()
    {
        return $this->_oPaymentInstructions;
    }

    /**
     * Get the total amount of the invoice.
     * Template getter.
     *
     * @return string
     */
    public function getTotalPrice()
    {
        $fTotal = $this->_oPaymentInstructions->getTotal();

        return $this->_getFormattedTotal($fTotal);
    }

    /**
     * Get the due date formatted in the current frontend language.
     * Template getter.
     *
     * @return string
     */
    public function getDueDate()
    {
        $sDate = '';

        if ($this->_oPaymentInstructions instanceof paypPayPalPlusPuiData) {
            $sDateFormatString = $this->_getDateFormatString();
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $this->_oPaymentInstructions->getDueDate());

            $sDate = $date->format($sDateFormatString);
        }

        return $sDate;
    }

    /**
     * Get the params array for the terms oxmultilangassign.
     * Template getter.
     *
     * @return array
     */
    public function getTermParams()
    {
        $sAmount = $sDueDate = '';

        if ($this->_oPaymentInstructions instanceof paypPayPalPlusPuiData) {
            $sDueDate = $this->getDueDate();
            $sAmount = $this->getTotalPrice() . ' ' . $this->_oPaymentInstructions->getCurrency();
        }

        return array($sAmount, $sDueDate);
    }

    /**
     * Set the Payment Instructions, if the order was paid with PayPal upon Invoice.
     */
    protected function _setPaymentInstructions()
    {
        $sOrderId = $this->_getOrderId();
        $oOrder = oxNew('oxOrder');
        if ($sOrderId && $oOrder->load($sOrderId)) {
            $this->_oPaymentInstructions = $oOrder->getPaymentInstructions();
        }
    }

    /**
     * Get the date format string according to the current frontend language.
     *
     * @return string
     */
    protected function _getDateFormatString()
    {
        $oConfig = oxNew('paypPayPalPlusConfig');

        return $oConfig->getShop()->translate('PAYP_PAYPALPLUS_PUI_SUCCESS_DATE_FORMAT');
    }


    /**
     * Format the order total as displayed in the payment instructions.
     * The formatting options are retrieved from the _lang.php files of the module.
     *
     * @param $fTotal
     *
     * @return mixed
     */
    protected function _getFormattedTotal($fTotal)
    {
        $sDecimals = $this->getShop()->getLang()->translateString('PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMALS');
        $sDecimalSeparator = $this->getShop()->getLang()->translateString('PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMAL_SEPARATOR');
        $sThousandsSeparator = oxRegistry::getLang()->translateString('PAYP_PAYPALPLUS_PUI_CURRENCY_THOUSANDS_SEPARATOR');

        $sTotal = number_format($fTotal, $sDecimals, $sDecimalSeparator, $sThousandsSeparator);;

        return str_replace('.', $sDecimalSeparator, $sTotal);
    }

    /**
     * Get the current Order Id from the basket.
     * Needed for testing.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _getOrderId()
    {
        $sOrderId = null;
        if ($this->_oBasket) {
            $sOrderId = $this->_oBasket->getOrderId();
        }

        return $sOrderId;
    }

    /**
     * Call the parent render method.
     * Needed for testing.
     *
     * @codeCoverageIgnore
     *
     * @return string
     */
    protected function _paypPayPalPlusPayment_render_parent()
    {
        return parent::render();
    }

    /**
     * Call the parent init method.
     * Needed for testing.
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusPayment_init_parent()
    {
        parent::init();
    }

    /**
     * Return Date Object for current date a.k.a today.
     * Needed for testing.
     *
     * @codeCoverageIgnore
     *
     * @return DateTime
     */
    protected function _getDateToday()
    {
        $oDateToday = new DateTime(date('Y-m-d'));

        return $oDateToday;
    }
}