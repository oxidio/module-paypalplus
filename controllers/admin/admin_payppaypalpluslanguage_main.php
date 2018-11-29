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
 * Class Admin_paypPayPalPlusLanguage_Main.
 *
 * Admin Order PayPal Plus tab controller.
 *
 * Collects and previews PayPal Plus payments data and controls.
 * the actions with them.
 *
 * Admin menu: Administer Orders -> Orders -> PayPal Plus
 */
class Admin_paypPayPalPlusLanguage_Main extends Admin_paypPayPalPlusLanguage_Main_parent
{
    /**
     * Extends parent functionality to additionally save PayPal Plus locale code
     * into database config aLanguageParams
     *
     * @inheritDoc
     */
    public function save()
    {
        $this->_callParentSave();

        $sOxId = $this->getEditObjectId();

        if ($sOxId != -1) {
            $aParams = $this->getConfig()->getRequestParameter("editval");
            $aLanguageParams = $this->getConfig()->getConfigParam('aLanguageParams');
            $aLanguageParams[$sOxId]['payppaypalplus_localecode'] = $aParams['payppaypalplus_localecode'];
            $this->getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $aLanguageParams);
        }
    }

    /**
     * Calls parent method
     *
     * @codeCoverageIgnore
     */
    protected function _callParentSave()
    {
        parent::save();
    }
}
