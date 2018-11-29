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
 * PayPal Plus Refund data list manager class.
 * Collects a list of refunds according to payment sale ID.
 */
class paypPayPalPlusRefundDataList extends oxList
{

    /**
     * Initialize the list model with base object and table names.
     *
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->init('paypPayPalPlusRefundData', 'payppaypalplusrefund');
    }


    /**
     * Load PayPal Plus refund models by sale ID and orders them by creation date and time.
     *
     * @param string $sSaleId
     */
    public function loadRefundsBySaleId($sSaleId)
    {
        $sSelect = sprintf(
            "SELECT * FROM `%s` WHERE `OXSALEID` = %s ORDER BY `OXDATECREATED`",
            $this->getBaseObject()->getCoreTableName(),
            paypPayPalPlusShop::getShop()->getDb()->quote($sSaleId)
        );

        $this->selectString($sSelect);
    }

    /**
     * Count and return a sum of all totals for refunds related to a given sale ID.
     * In other words, it counts already refunded total amount for a payment.
     *
     * @param string $sSaleId
     *
     * @return double
     */
    public function getRefundedSumBySaleId($sSaleId)
    {
        $oDb = paypPayPalPlusShop::getShop()->getDb();

        $sQuery = sprintf(
            "SELECT SUM(`OXTOTAL`) FROM `%s` WHERE `OXSALEID` = %s",
            $this->getBaseObject()->getCoreTableName(),
            $oDb->quote($sSaleId)
        );

        return (double) $oDb->getOne($sQuery);
    }
}
