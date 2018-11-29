-- PAYP PayPal Plus module uninstall SQL file --

-- Set PayPal Plus method not active
-- NOTE: It should not be deleted - it might case serious data loss in payments/orders history!
UPDATE `oxpayments` SET `OXACTIVE` = 0 WHERE `OXID` = 'payppaypalplus';

-- Unassign the PayPal Plus from all countries and shipping methods.
DELETE FROM `oxobject2payment` WHERE `OXPAYMENTID` = 'payppaypalplus';

-- Unassign the PayPal Plus from all user groups.
DELETE FROM `oxobject2group` WHERE `OXOBJECTID` = 'payppaypalplus';

-- Completely remove PayPAl Plus payment and refund transactions tables from database
-- NOTE: It is not recommended - refunds history and paymens data will be lost!
-- DROP TABLE IF EXISTS `payppaypalpluspayment`;
-- DROP TABLE IF EXISTS `payppaypalplusrefund`;
-- DROP TABLE IF EXISTS `payppaypalpluspui`;

-- Reactivate default payment methods, which were disabled on module installation ("oxiddebitnote" and "oxidcreditcard")
-- NOTE: Execute reactivation only if the methods are needed
-- UPDATE `oxpayments` SET `OXACTIVE` = 1 WHERE `OXID` IN ('oxiddebitnote', 'oxidcreditcard');
