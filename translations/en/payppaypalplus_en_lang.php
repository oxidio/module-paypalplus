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
    'charset'                                               => 'UTF-8',

    'PAYP_PAYPALPLUS_ERROR_NOPAYMENT'                       => 'Sorry, PayPal Plus is currently not available.<br/>' .
                                                               'Please update Your basket and try again or contact shop administrator.',
    'PAYP_PAYPALPLUS_ERROR_ADDRESS'                         => 'Please make sure shipping address is correct, otherwise PayPal Plus payment might be not available.',
    'PAYP_PAYPALPLUS_ERROR_SHIPPING_PHONE'                  => 'Phone number on shipping address is invalid. It should match E.123 notation, e.g. +31 42 1123 4567 or +314211234567. Max length 50 symbols.',
    'PAYP_PAYPALPLUS_ERROR_BILLING_PHONE'                   => 'Phone number on billing address is invalid. It should match E.123 notation, e.g. +31 42 1123 4567 or +314211234567. Max length 50 symbols.',
    'PAYP_PAYPALPLUS_ERROR_SHIPPING_PHONE_1'                => 'Phone number on shipping address is invalid. It should match E123 notation, e.g. +31 42 1123 4567, +314211234567, (314)211234567 or (314)211 234 567. Max length 50 symbols.',
    'PAYP_PAYPALPLUS_ERROR_BILLING_PHONE_1'                 => 'Phone number on billing address is invalid. It should match E123 notation, e.g. +31 42 1123 4567, +314211234567, (314)211234567 or (314)211 234 567. Max length 50 symbols.',
    'PAYP_PAYPALPLUS_ERROR_NO_ORDER'                        => 'Could not load order. Please contact administrator.',
    'PAYP_PAYPALPLUS_METHOD_LABEL'                          => 'Processed by PayPal',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_PAYMENT_INSTRUCTIONS'      => 'Payment Instructions',
    // Date formatting for invoice
    'PAYP_PAYPALPLUS_PUI_SUCCESS_DATE_FORMAT'               => 'Y-m-d',
    // Currency formatting for invoice
    'PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMALS'                 => '2',
    'PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMAL_SEPARATOR'        => '.',
    'PAYP_PAYPALPLUS_PUI_CURRENCY_THOUSANDS_SEPARATOR'      => ',',

    'PAYP_PAYPALPLUS_PUI_SUCCESS_DESCRIPTION'               => '<span class="bold">Why PayPal?</span> PayPal is our partner for processing invoice payments. PayPal has just transferred the amount to us directly. You pay the amount to PayPal according to the payment instructions after you have received and checked your purchase.',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_PRINT_INSTRUCTIONS'        => 'Print',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_LEGAL_NOTICE'              => '%s has assigned the claim against you to PayPal (Europe) S.&agrave;r.l. et Cie, S.C.A. You will only be released from your payment obligation upon payment  to PayPal (Europe) S.&agrave;r.l. et Cie, S.C.A.',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_TERM'                      => 'Please transfer %s by %s to PayPal',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER'            => 'Benificiary',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME'                 => 'Bank',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT'                    => 'Amount',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER'          => 'Reference Number',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN'                      => 'IBAN',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_BIC'                       => 'BIC',


    'PAYP_PAYPALPLUS_ERR_WALL_PLACEHOLDER_1'                => 'Element "%s" is not present on template. Check variable "sWallPlaceholderId" on "payppaypalpluswall.tpl".',
    'PAYP_PAYPALPLUS_ERR_EXT_BUTTON_1'                      => 'Element "%s" is not present on template. Check variable "sExtenalButtonId" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusNextButtonId".',
    'PAYP_PAYPALPLUS_ERR_LOADING_1'                         => 'Element "%s" is not present on template. Check variable "sExtenalButtonId" on "payppaypalpluswall.tpl".',
    'PAYP_PAYPALPLUS_ERR_NEXT_LINK_1'                       => 'Top next link "%s" is not present on template. Check variable "sNextStepLink" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusNextLink".',
    'PAYP_PAYPALPLUS_ERR_NEXT_LINK_PARENT_1'                => 'Element "%s" (parent of "paypPayPalPlusNextLink") is not present on template. Check variable "sNextStepLinkParent" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusNextLinkParent".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_RADIO_BTN_1'               => 'Element "%s" is not present on template. Check variable "sPaymentRadioButton" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusPaymentRadio".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_DL_1'                      => 'Element "%s" is not present on template. Check variable "sPaymentListItem" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusListItem".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_DT_1'                      => 'Element "%s" is not present on template. Check variable "sPaymentListItemTitle" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusListItemTitle".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_1'                   => 'Payment label "%s" is not present on template. Check variable "sPaymentLabelFormat" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusLabelFormat".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_CHILD_1'             => 'Element "%s" is not present on template. Check variable "sPaymentLabelChild" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusLabelChild".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_DESC_1'                    => 'The description for payment method "%s" is not present. Please, add it for all languages under "eShop Admin > Shop Settings > Payment Methods > Payment method > description". Also check variable "sPaymentDescription" (child element of "paypPayPalPlusListItem") on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusDescription" and reassure that the defined selector is present in the page source code.',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_1'                  => 'Element "%s" is not present on template. Check variable "sPaymentIdPrefix" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusMethodIdPrefix".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_2'                  => 'Element "%s" is not present on template. Check payment is in right containers. Check variable "sPaymentListItem" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusListItem". Also check variable "sPaymentListItemTitle" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusListItemTitle".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_3'                  => 'Element "%s" should also match selector "%s". Check variable "sPaymentIdPrefix" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusMethodIdPrefix". Also check variable "sPaymentRadioButton" on "payppaypalpluswall.tpl" and module setting "paypPayPalPlusPaymentRadio".',
    'PAYP_PAYPALPLUS_SETTINGS_TPL_LOCATION'                 => 'Module settings are on page "eShop Admin > Extensions > Settings > PayPal Plus > PayPal Plus Integration Settings"',
    'PAYP_PAYPALPLUS_ERROR_NO_PAYMENT_FOUND_FOR_EVENT'      => 'Error: There is no payment data stored in the shop for this event',
    'PAYP_PAYPALPLUS_ERROR_PAYMENT_DATA_NOT_SAVED'          => 'Error: The payment data could not be stored',
    'PAYP_PAYPALPLUS_ERROR_PAYMENT_NOT_VALID'               => 'Error: The payment data could not be executed. Teh Execution call returned an invalid state',
    'PAYP_PAYPALPLUS_ERROR_NO_USER_CREDENTIALS'             => 'Hint: Please configure API Settings in the "Settings" tab to be able to use PayPal Webhooks. Then deactivate and activate this module again.',
    'PAYP_PAYPALPLUS_ERROR_PAYPAL_ERROR_OR_SESSION_EXPIRED' => 'PayPal returned an error. Please try another PayPal checkout or select an alternative payment method',
    'PAYP_PAYPALPLUS_TEST'                                  => 'english string with &uuml;',
);