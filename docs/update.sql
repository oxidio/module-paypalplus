-- If PayPal Plus module version was updated to v1.0.3 or higher, please execute this script in eShop MySQL database

-- Fix collation for PayPal Plus tables fields
ALTER TABLE `payppaypalpluspayment`
  CHANGE `OXSALEID` `OXSALEID` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment sale id',
  CHANGE `OXPAYMENTID` `OXPAYMENTID` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment id';

ALTER TABLE `payppaypalplusrefund`
  CHANGE `OXSALEID` `OXSALEID` VARCHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'PayPal Plus payment sale id';
