-- PAYP PayPal Plus module migration SQL file --

-- Copy the PayPal Plus payments from oxps module to payp module
CREATE TABLE IF NOT EXISTS `payppaypalpluspayment` (
                `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Payment oxid id',
                `OXORDERID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Order id',
                `OXSALEID` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment sale id',
                `OXPAYMENTID` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment id',
                `OXSTATUS` varchar(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment status',
                `OXDATECREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Payment creation date',
                `OXTOTAL` double NOT NULL DEFAULT '0' COMMENT 'Total payment amount',
                `OXCURRENCY` varchar(32) NOT NULL DEFAULT '' COMMENT 'Payment currency',
                `OXPAYMENTOBJECT` BLOB NOT NULL DEFAULT '' COMMENT 'Serialized payment object',
                PRIMARY KEY (`OXID`),
                UNIQUE `OXORDERID` (`OXORDERID`),
                UNIQUE `OXSALEID` (`OXSALEID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PayPal Plus payment data model';
INSERT IGNORE INTO `payppaypalpluspayment` SELECT * FROM `oxpspaypalpluspayment`;

-- Copy the PayPal Plus payments upon invoice from oxps module to payp module
CREATE TABLE IF NOT EXISTS `payppaypalpluspui` (
                `OXID` CHAR(32) NOT NULL COMMENT 'Payment oxid id' COLLATE 'latin1_general_ci',
                `OXPAYMENTID` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment id' COLLATE 'latin1_general_ci',
                `OXREFERENCENUMBER` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI reference_number',
                `OXBANKNAME` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction bank name',
                `OXACCOUNTHOLDER` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction account holder',
                `OXIBAN` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction IBAN',
                `OXBIC` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI banking instruction BIC',
                `OXDUEDATE` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'PayPal Plus PuI due date',
                `OXTOTAL` DOUBLE NOT NULL DEFAULT '0' COMMENT 'PayPal Plus PuI Total invoice amount',
                `OXCURRENCY` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus PuI invoice currency',
                `OXPUIOBJECT` TEXT NOT NULL COMMENT 'JSON representation of the payment instructions',
                PRIMARY KEY (`OXID`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PayPal Plus Pay upon Invoice data model';
INSERT IGNORE INTO `payppaypalpluspui` SELECT * FROM `oxpspaypalpluspui`;

-- Copy the PayPal Plus refunds from oxps module to payp module
CREATE TABLE IF NOT EXISTS `payppaypalplusrefund` (
                `OXID` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Payment oxid id',
                `OXSALEID` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment sale id',
                `OXREFUNDID` varchar(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus refund id',
                `OXSTATUS` varchar(32) NOT NULL DEFAULT '' COMMENT 'PayPal Plus refund status',
                `OXDATECREATED` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Refund creation date',
                `OXTOTAL` double NOT NULL DEFAULT '0' COMMENT 'Total refund amount',
                `OXCURRENCY` varchar(32) NOT NULL DEFAULT '' COMMENT 'Refund currency',
                `OXREFUNDOBJECT` BLOB NOT NULL DEFAULT '' COMMENT 'Serialized refund object',
                PRIMARY KEY (`OXID`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='PayPal Plus refund data model';
INSERT IGNORE INTO `payppaypalplusrefund` SELECT * FROM `oxpspaypalplusrefund`;

-- Copy the configuration from the old module to the new one
INSERT IGNORE INTO `oxconfig`
  SELECT
    CONCAT('payp', SUBSTRING(OXID, 5, CHAR_LENGTH(OXID)))           AS OXID,
    OXSHOPID,
    CONCAT(LEFT(OXMODULE, 7), 'payp', SUBSTRING(OXMODULE FROM 12))  AS OXMODULE,
    CONCAT('payp', SUBSTRING(OXVARNAME, 5, CHAR_LENGTH(OXVARNAME))) AS OXVARNAME,
    OXVARTYPE,
    OXVARVALUE,
    OXTIMESTAMP
  FROM `oxconfig`
  WHERE OXMODULE = 'module:oxpspaypalplus';

--  Update oxorder table, set payment type from 'oxpspaypalplus' to 'payppaypalplus '
UPDATE oxorder
SET oxorder.OXPAYMENTTYPE = 'payppaypalplus'
WHERE oxorder.OXPAYMENTTYPE = 'oxpspaypalplus';

--  Update oxuserpayments table, set payment type from 'oxpspaypalplus' to 'payppaypalplus '
UPDATE oxuserpayments
SET oxuserpayments.OXPAYMENTSID = 'payppaypalplus'
WHERE oxuserpayments.OXPAYMENTSID = 'oxpspaypalplus';

-- Set oxps modules PayPal Plus method not active
-- We will NOT delete it here. Use the uninstall script of the oxps PayPal Module after verifying correct migration.
UPDATE `oxpayments`
SET `OXACTIVE` = 0, `OXDESC` = 'PayPal Plus old'
WHERE `OXID` = 'oxpspaypalplus';

-- Unassign the oxps modules PayPal Plus Payment from all countries and shipping methods.
DELETE FROM `oxobject2payment`
WHERE `OXPAYMENTID` = 'oxpspaypalplus';

-- Unassign the oxps modules PayPal Plus from all user groups.
DELETE FROM `oxobject2group`
WHERE `OXOBJECTID` = 'oxpspaypalplus';