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

$sLangName = 'English';

$aLang = array(
    'charset'                                          => 'UTF-8',
    'tbclorder_paypalplus'                             => 'PayPal Plus',

    // Settings interface translations
    'SHOP_MODULE_GROUP_paypPayPalPlusApi'              => 'API Settings',
    'SHOP_MODULE_paypPayPalPlusClientId'               => 'Client ID',
    'SHOP_MODULE_paypPayPalPlusSecret'                 => 'Secret',

    'SHOP_MODULE_GROUP_paypPayPalPlusSandbox'          => 'Sandbox API Settings',
    'SHOP_MODULE_paypPayPalPlusSandbox'                => 'Sandbox mode',
    'SHOP_MODULE_paypPayPalPlusSandboxClientId'        => 'Sandbox Client ID',
    'SHOP_MODULE_paypPayPalPlusSandboxSecret'          => 'Sandbox Secret',

    'SHOP_MODULE_GROUP_paypPayPalPlusIntegration'      => 'PayPal Plus integration Settings',
    'SHOP_MODULE_paypPayPalPlusExternalMethods'        => 'IDs of payment methods, which should be displayed inside PayPal Plus wall widget.<br/>' .
                                                          'Methods statuses and sort orders are set up in <i>Shop Settings -> Payment Methods</i><br/>' .
                                                          'Notice, that OXID methods "oxiddebitnote" and "oxidcreditcard" could not be displayed in the wall.<br>' .
                                                          'Maximum 5 payment methods are accepted in the wall.',

    'SHOP_MODULE_GROUP_paypPayPalPlusTemplateIntegration'    => 'PayPal Plus Template integration settings',
    'SHOP_MODULE_paypPayPalPlusNextButtonId'           => '* [paypPayPalPlusNextButtonId] An ID of "next" button on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusNextLink'               => '[paypPayPalPlusNextLink] Selector for top navigation next step link on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusNextLinkParent'         => '[paypPayPalPlusNextLinkParent] Selector for a parent element of the top navigation next step link on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusPaymentRadio'           => '* [paypPayPalPlusPaymentRadio] Selector for a default radio buttons (for payment methods selection) on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusListItem'               => '* [paypPayPalPlusListItem] Selector for a payment methods list item on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusListItemTitle'          => '[paypPayPalPlusListItemTitle] Selector for a title element of the payment methods list item on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusLabelFormat'            => '[paypPayPalPlusLabelFormat] Format of selector for payment method label element on checkout payment page. ' .
                                                          'Notice, that if "%s" is used inside the format string, it is replaced by a dynamic value - payment method ID.',
    'SHOP_MODULE_paypPayPalPlusLabelChild'             => '[paypPayPalPlusLabelChild] Selector for a child element of the payment method label ("paypPayPalPlusLabelFormat") on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusDescription'            => '[paypPayPalPlusDescription] Selector for a payment method description tag on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMethodIdPrefix'         => '[paypPayPalPlusMethodIdPrefix] Prefix for a payment method ID attribute used in input elements on checkout payment page.',

    'SHOP_MODULE_GROUP_paypPayPalPlusMobIntegration'      => 'PayPal Plus Mobile template integration settings',
    'SHOP_MODULE_paypPayPalPlusMobNextButtonId'           => '* [paypPayPalPlusNextButtonId] An ID of "next" button on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobNextLink'               => '[paypPayPalPlusNextLink] Selector for top navigation next step link on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobNextLinkParent'         => '[paypPayPalPlusNextLinkParent] Selector for a parent element of the top navigation next step link on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobPaymentRadio'           => '* [paypPayPalPlusPaymentRadio] Selector for a default radio buttons (for payment methods selection) on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobListItem'               => '* [paypPayPalPlusListItem] Selector for a payment methods list item on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobListItemTitle'          => '[paypPayPalPlusListItemTitle] Selector for a title element of the payment methods list item on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobLabelFormat'            => '[paypPayPalPlusLabelFormat] Format of selector for payment method label element on checkout payment page. ' .
        'Notice, that if "%s" is used inside the format string, it is replaced by a dynamic value - payment method ID.',
    'SHOP_MODULE_paypPayPalPlusMobLabelChild'             => '[paypPayPalPlusLabelChild] Selector for a child element of the payment method label ("paypPayPalPlusLabelFormat") on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobDescription'            => '[paypPayPalPlusDescription] Selector for a payment method description tag on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusMobMethodIdPrefix'         => '[paypPayPalPlusMethodIdPrefix] Prefix for a payment method ID attribute used in input elements on checkout payment page.',

    'SHOP_MODULE_GROUP_paypPayPalPlusFlowIntegration'      => 'PayPal Plus Flow template integration settings',
    'SHOP_MODULE_paypPayPalPlusFlowNextButtonId'           => '* [paypPayPalPlusNextButtonId] An ID of "next" button on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowNextLink'               => '[paypPayPalPlusNextLink] Selector for top navigation next step link on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowNextLinkParent'         => '[paypPayPalPlusNextLinkParent] Selector for a parent element of the top navigation next step link on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowPaymentRadio'           => '* [paypPayPalPlusPaymentRadio] Selector for a default radio buttons (for payment methods selection) on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowListItem'               => '* [paypPayPalPlusListItem] Selector for a payment methods list item on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowListItemTitle'          => '[paypPayPalPlusListItemTitle] Selector for a title element of the payment methods list item on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowLabelFormat'            => '[paypPayPalPlusLabelFormat] Format of selector for payment method label element on checkout payment page. ' .
        'Notice, that if "%s" is used inside the format string, it is replaced by a dynamic value - payment method ID.',
    'SHOP_MODULE_paypPayPalPlusFlowLabelChild'             => '[paypPayPalPlusLabelChild] Selector for a child element of the payment method label ("paypPayPalPlusLabelFormat") on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowDescription'            => '[paypPayPalPlusDescription] Selector for a payment method description tag on checkout payment page.',
    'SHOP_MODULE_paypPayPalPlusFlowMethodIdPrefix'         => '[paypPayPalPlusMethodIdPrefix] Prefix for a payment method ID attribute used in input elements on checkout payment page.',

    'SHOP_MODULE_paypPayPalPlusValidateTemplate'       => 'Validate PayPal Plus template integration',
    'SHOP_MODULE_paypPayPalPlusInvNr'                  => 'Transmit the Order No. as "Invoice Number" to PayPal (This can lead to gaps in order numbering)',
    'HELP_SHOP_MODULE_paypPayPalPlusInvNr'             => 'Due to the architecture of the PayPal API, the order number will be reserved when selecting the payment method and not during order finalizing. ' .
                                                          'If an order is never finalized, its order number will not be reassigned to a different order and thus is missing in the order numbering.',


    'SHOP_MODULE_GROUP_paypPayPalPlusOther'            => 'Logging, Debugging and Connectivity Settings',
    'SHOP_MODULE_paypPayPalPlusLogEnabled'             => 'Enable PayPal API data logging to file',
    'SHOP_MODULE_paypPayPalPlusLogFile'                => 'PayPal API log file name inside eShop log/ folder',
    'SHOP_MODULE_paypPayPalPlusLogLevel'               => 'PayPal API logging level',
    'SHOP_MODULE_paypPayPalPlusLogLevel_DEBUG'         => 'DEBUG (Use in SANDBOX mode only!)',
    'SHOP_MODULE_paypPayPalPlusLogLevel_INFO'          => 'INFO',
    'SHOP_MODULE_paypPayPalPlusLogLevel_WARN'          => 'WARN',
    'SHOP_MODULE_paypPayPalPlusLogLevel_ERROR'         => 'ERROR',
    'SHOP_MODULE_paypPayPalPlusValidation'             => 'PayPal API data validation level',
    'SHOP_MODULE_paypPayPalPlusValidation_log'         => 'log',
    'SHOP_MODULE_paypPayPalPlusValidation_strict'      => 'strict',
    'SHOP_MODULE_paypPayPalPlusValidation_disabled'    => 'disabled',
    'SHOP_MODULE_paypPayPalPlusTimeout'                => 'PayPal API connection timeout in seconds',
    'SHOP_MODULE_paypPayPalPlusRetry'                  => 'A number of PayPal API connection retries',
    'SHOP_MODULE_paypPayPalPlusDebug'                  => 'Enable PayPal API payment exceptions debugging mode on front end',
    'SHOP_MODULE_paypPayPalPlusSaveToFile'             => 'Save executed payments data to a text file inside eShop log/ folder',
    'SHOP_MODULE_paypPayPalPlusPhoneInternationalOnly' => 'Allow only international phone format E.123 (starts with "+").',

    'SHOP_MODULE_GROUP_paypPayPalPlusPUI'              => 'Other settings',
    'SHOP_MODULE_paypPayPalPlusShopOwnerStr'           => 'Name of the shop owner as stated on "Payment upon Invoice" invoices',
    'HELP_SHOP_MODULE_paypPayPalPlusShopOwnerStr'      => 'Name of the shop owner as stated in the legal notice section on "Payment upon Invoice" invoices. If this field is empty the Company Name will be taken from the shop core settings.',
    'SHOP_MODULE_paypPayPalPlusDiscountRefunds'        => 'Discount PayPal refunds off the order total',
    'HELP_SHOP_MODULE_paypPayPalPlusDiscountRefunds'   => 'This setting is important, if PayPal Plus "Payment upon Invoice" is offered, as the shop owner has to generate invoices and ' .
                                                          'the invoice total has to match the order total.',

    'SHOP_MODULE_paypPayPalPlusRefundOnInvoice'        => 'Disable refunds on orders, for which an invoice has already been generated within the OXID eShop',

    'HELP_SHOP_MODULE_paypPayPalPlusRefundOnInvoice'   => 'If the invoices are generated outside the OXID eShop, verify that the fields OXBILLNR or OXINVOICENR in the table oxorders are updated after the invoice is generated.',
    'SHOP_MODULE_paypPayPalPlusEstimatedDeliveryDate'  => 'Transfer estimated delivery date of the articles in the basket to PayPal',


    // Order tab "PayPal Plus" translations
    'PAYP_PAYPALPLUS_ONLY_FOR_PAYPAL_PLUS_PAYMENT'     => 'This tab is valid only for orders payed using PayPal Plus payment method.',

    'PAYP_PAYPALPLUS_PAYMENT_OVERVIEW'                 => 'Payment overview',
    'PAYP_PAYPALPLUS_PAYMENT_STATUS'                   => 'Payment status',
    'PAYP_PAYPALPLUS_ORDER_AMOUNT'                     => 'Payment total',
    'PAYP_PAYPALPLUS_REFUNDED_AMOUNT'                  => 'Refunded amount',
    'PAYP_PAYPALPLUS_PAYMENT_ID'                       => 'Payment ID',
    'PAYP_PAYPALPLUS_PAYMENT_METHOD'                   => 'Payment method',

    'PAYP_PAYPALPLUS_PAYMENT_REFUNDING'                => 'Refunds',
    'PAYP_PAYPALPLUS_AVAILABLE_REFUNDS'                => 'Remaining number of refund operation:',
    'PAYP_PAYPALPLUS_AVAILABLE_REFUND_AMOUNT'          => 'Remaining payment amount to refund:',
    'PAYP_PAYPALPLUS_DATE'                             => 'Date',
    'PAYP_PAYPALPLUS_AMOUNT'                           => 'Amount',
    'PAYP_PAYPALPLUS_STATUS'                           => 'Status',
    'PAYP_PAYPALPLUS_NEW_REFUND'                       => 'Submit a refund',
    'PAYP_PAYPALPLUS_REFUND'                           => 'Refund',

    'PAYP_PAYPALPLUS_PUI'                              => 'Payment upon invoice',
    'PAYP_PAYPALPLUS_PUI_PAYMENT_INSTRUCTIONS'         => 'Payment Instructions',
    'PAYP_PAYPALPLUS_PUI_TERM'                         => 'Term',
    'PAYP_PAYPALPLUS_PUI_ACCOUNT_HOLDER'               => 'Benificiary',
    'PAYP_PAYPALPLUS_PUI_BANK_NAME'                    => 'Bank',
    'PAYP_PAYPALPLUS_PUI_AMOUNT'                       => 'Amount',
    'PAYP_PAYPALPLUS_PUI_REFERENCE_NUMBER'             => 'Reference Number',
    'PAYP_PAYPALPLUS_PUI_IBAN'                         => 'IBAN',
    'PAYP_PAYPALPLUS_PUI_BIC'                          => 'BIC',

    /** Admin order list */
    'PAYP_PAYPALPLUS_LIST_STATUS_ALL'                  => 'All',

    /** Error messages */
    'PAYP_PAYPALPLUS_ERR_INVALID_REQUEST'              => 'Invalid request! Please refresh the page and try again.',
    'PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT'               => 'Please enter a positive decimal number.',
    'PAYP_PAYPALPLUS_ERR_REFUND_NOT_POSSIBLE'          => 'Refunds are not possible anymore for the current payment.',
    'PAYP_PAYPALPLUS_ERR_REFUND_API_EXCEPTION'         => 'Refund request failed! Please try again or contact Your eShop administrator.',
    'PAYP_PAYPALPLUS_ERR_PAYMENTSTATUS_NOT_FOUND'      => 'A related PayPal Plus Payment could not be found. Please make sure, that this order really was paid via PayPal',
    'PAYP_PAYPALPLUS_ERR_PAYMENTSTATUS_NOT_UPDATED'    => 'The status of the related PayPal Plus Payment could not be set to "completed".',
    'PAYP_PAYPALPLUS_ERR_INVOICE_UPDATE_REQUIRED'      => 'The refund transaction was successful. Please, do not forget to update the invoice related to this order.',
    'PAYP_PAYPALPLUS_ERR_INVOICE_EXISTS'               => 'A refund cannot be made for this order as an invoice has already been created.',

    'PAYP_PAYPALPLUS_COUNTRY_LOCALE_CODE'              => 'PayPal Plus locale code',
    'HELP_PAYP_PAYPALPLUS_COUNTRY_LOCALE_CODE'         => 'Look up valid PayPal locale codes for your country at this URL https://developer.paypal.com/docs/classic/api/locale_codes/',

    'SHOP_MODULE_GROUP_paypPayPalPlusExperience'       => 'PayPal Payment Experience settings',
    'SHOP_MODULE_paypPayPalPlusExpProfileId'           => 'Experience Profile Id. You do not need to fill fields below if you have it already, otherwise profile id will be generated when fields below will be filled',
    'SHOP_MODULE_paypPayPalPlusExpName'                => '* Name of the web experience profile',
    'SHOP_MODULE_paypPayPalPlusExpBrand'               => 'A label that overrides the business name in the PayPal account on the PayPal pages',
    'SHOP_MODULE_paypPayPalPlusExpLogo'                => 'A URL to logo image. Allowed vaues: .gif, .jpg, or .png',
    'SHOP_MODULE_paypPayPalPlusExpLocale'              => 'Locale of pages displayed by PayPal payment experience',

    'PAYP_PAYPALPLUS_ERR_EXP_NAME_EMPTY'               => 'Name of the web experience profile cannot be empty',
    'PAYP_PAYPALPLUS_ERROR_NO_USER_CREDENTIALS'        => 'Hint: Please configure API Settings in the "Settings" tab to be able to use PayPal Webhooks. Then deactivate and activate this module again.',
    'PAYP_PAYPALPLUS_ERROR_NOSSL_URL'                  => 'Hint: Please configure a SSL Url in config.inc.php to be able to use PayPal Webhooks. Then deactivate and activate this module again.',
);
