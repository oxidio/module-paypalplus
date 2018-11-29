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
class Admin_paypPayPalPlusOrder_List extends Admin_paypPayPalPlusOrder_List_parent
{

    /**
     * Executes parent method parent::render() and returns name of template
     * file "order_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        $sTemplate = parent::render();

        $aPayments = array();
        $sPayment = oxRegistry::getConfig()->getRequestParameter('payppaypalpluspayment');

        $this->_aViewData['payppaypalpluspayment'] = $sPayment ? $sPayment : -1;

        /** @var oxList $oPaymentList */
        $oPaymentList = oxNew('oxList');
        $oPaymentList->init('oxPayment');
        foreach ($oPaymentList->getList() as $oPayment) {
            $aPayments[$oPayment->getId()] = array('name' => $oPayment->oxpayments__oxdesc->value);
        }

        $aPayments['pui'] = array('name' => 'PayPal Plus Rechnungskauf');

        $this->_aViewData['aPayments'] = $aPayments;

        return $sTemplate;
    }

    /**
     * Adding folder check.
     *
     * @param array  $aWhere  SQL condition array.
     * @param string $sqlFull SQL query string.
     *
     * @return string
     */
    protected function _prepareWhereQuery($aWhere, $sqlFull)
    {
        $oDb = oxDb::getDb();
        $sQ = parent::_prepareWhereQuery($aWhere, $sqlFull);

        $sPayment = oxRegistry::getConfig()->getRequestParameter("payppaypalpluspayment");
        if ($sPayment && $sPayment != '-1') {
            if ($sPayment == 'pui') {
                $sQ .= " and ( payppaypalpluspui.oxid  IS NOT NULL )";
            } else {
                $sQ .= " and ( oxorder.oxpaymenttype = " . $oDb->quote($sPayment) . " ) and ( payppaypalpluspui.oxid IS NULL )";
            }
        }

        return $sQ;
    }

    /**
     * Builds and returns SQL query string. Adds additional order check.
     *
     * @param object $oListObject list main object.
     *
     * @return string
     */
    protected function _buildSelectString($oListObject = null)
    {
        $sSql = parent::_buildSelectString($oListObject);

        $sPaymentTable = getViewName("oxpayments");

        $sQ = ", pluspayments.oxdesc AS payments_oxdesc, payppaypalpluspui.oxid AS payppaypalpluspui_oxid from oxorder
            LEFT JOIN `" . $sPaymentTable . "` AS pluspayments ON pluspayments.oxid = oxorder.oxpaymenttype
            LEFT JOIN payppaypalpluspayment ON payppaypalpluspayment.OXORDERID = oxorder.OXID
            LEFT JOIN payppaypalpluspui ON payppaypalpluspui.OXPAYMENTID = payppaypalpluspayment.OXPAYMENTID
        ";

        $sSql = str_replace('from oxorder', $sQ, $sSql);

        return $sSql;
    }


    /**
     * Adds order by to SQL query string.
     *
     * @param string $sSql sql string
     *
     * @return string
     */
    protected function _prepareOrderByQuery($sSql = null)
    {
        $sSql = parent::_prepareOrderByQuery($sSql);

        $aSorting = parent::getListSorting();
        if ($aSorting['oxorder']['oxpaymenttype']) {
            $sQ = ' ORDER BY payments_oxdesc, IF(ISNULL(payppaypalpluspui_oxid), 0, 1), oxorder.oxbillnr, ';
            $sSql = str_replace('order by ', $sQ, $sSql);
        }

        return $sSql;
    }
}