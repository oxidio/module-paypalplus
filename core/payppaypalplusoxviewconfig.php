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
 * Class paypPayPalPlusOxViewConfig.
 * Overloads oxViewConfig class.
 *
 * @see oxViewConfig
 */
class paypPayPalPlusOxViewConfig extends paypPayPalPlusOxViewConfig_parent
{

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oPayPalPlusShop = null;


    /**
     * Get OXID eShop wrapper.
     *
     * @return paypPayPalPlusShop
     */
    public function getPayPalPlusShop()
    {
        if (is_null($this->_oPayPalPlusShop)) {
            $this->_oPayPalPlusShop = paypPayPalPlusShop::getShop();
        }

        return $this->_oPayPalPlusShop;
    }


    /**
     * Get an ID of a PayPal Plus payment method.
     *
     * @return string
     */
    public function getPayPalPlusMethodId()
    {
        return $this->getPayPalPlusShop()->getPayPalPlusConfig()->getPayPalPlusMethodId();
    }

    /**
     * Get a label for PayPal Plus method name to use on order preview page.
     *
     * @return string
     */
    public function getPayPalPlusMethodLabel()
    {
        return $this->getPayPalPlusShop()->translate('PAYP_PAYPALPLUS_METHOD_LABEL');
    }

    /**
     * Check if PayPal Plus is active.
     * Module and payment method statuses are checked.
     *
     * @return bool
     */
    public function isPayPalPlusActive()
    {
        $oValidator = $this->getPayPalPlusShop()->getValidator();

        return ($oValidator->isModuleActive() and $oValidator->isPaymentActive());
    }

    /**
     * Check if PayPal Plus services are available.
     * Checks is PayPal payment was created within the API and stored to session.
     *
     * @return bool
     */
    public function isPayPalPlusAvailable()
    {
        return $this->getPayPalPlusShop()->getValidator()->isPaymentCreated();
    }

    /**
     * Get the module resource URL.
     *
     * @param string $sResourceRelativePath Media resource path inside the module `src` folder.
     *
     * @return string
     */
    public function getPayPalPlusSrcUrl($sResourceRelativePath = '')
    {
        /** @var paypPayPalPlusOxViewConfig|oxViewConfig $this */

        $oModule = $this->getPayPalPlusShop()->getPayPalPlusModule();

        return $this->getModuleUrl($oModule->getModulePath(), 'out/src/' . $sResourceRelativePath);
    }
}
