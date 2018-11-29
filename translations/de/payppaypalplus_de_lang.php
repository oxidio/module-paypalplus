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

$sLangName = 'Deutsch';

$aLang = array(
    'charset'                                               => 'UTF-8',

    'PAYP_PAYPALPLUS_ERROR_NOPAYMENT'                       => 'Entschuldigung, PayPal Plus ist momentan nicht verf&uuml;gbar.<br/>' .
                                                               'Bitte aktualisieren Sie Ihren Warenkorb und versuchen es erneut oder kontaktieren Sie den Shop-Administrator.',
    'PAYP_PAYPALPLUS_ERROR_ADDRESS'                         => 'Bitte stellen Sie sicher, dass die Versandadresse korrekt ist, ansonsten ist die Zahlungsart PayPal Plus m&ouml;glicherweise nicht verf&uuml;gbar.',
    'PAYP_PAYPALPLUS_ERROR_SHIPPING_PHONE'                  => 'Das Format der Telefonnummer der Lieferanschrift ist ung&uuml;ltig. Sie muss im Format E.123 angegeben werden, z.B. +31 42 1123 4567 oder +314211234567. Maximal 50 Zeichen.',
    'PAYP_PAYPALPLUS_ERROR_BILLING_PHONE'                   => 'Das Format der Telefonnummer der Rechnungsanschrift ist ung&uuml;ltig. Sie muss im Format E.123 angegeben werden, z.B. +31 42 1123 4567 oder +314211234567. Maximal 50 Zeichen.',
    'PAYP_PAYPALPLUS_ERROR_SHIPPING_PHONE_1'                => 'Das Format der Telefonnummer der Lieferanschrift ist ung&uuml;ltig. Sie muss im Format E.123 angegeben werden, z.B. +31 42 1123 4567, +314211234567, (314)211234567 or (314)211 234 567. Maximal 50 Zeichen.',
    'PAYP_PAYPALPLUS_ERROR_BILLING_PHONE_1'                 => 'Das Format der Telefonnummer der Rechnungsanschrift ist ung&uuml;ltig. Sie muss im Format E.123 angegeben werden, z.B. +31 42 1123 4567, +314211234567, (314)211234567 or (314)211 234 567. Maximal 50 Zeichen.',

    'PAYP_PAYPALPLUS_ERROR_NO_ORDER'                        => 'Die Bestellung konnte nicht geladen werden. Bitte kontaktieren Sie Ihren Administrator.',
    'PAYP_PAYPALPLUS_METHOD_LABEL'                          => 'Durchgef&uuml;hrt von PayPal',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_PAYMENT_INSTRUCTIONS'      => 'Zahlungshinweise',
    // Date formatting for invoice
    'PAYP_PAYPALPLUS_PUI_SUCCESS_DATE_FORMAT'               => 'j.n.Y',
    // Currency formatting for invoice
    'PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMALS'                 => '2',
    'PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMAL_SEPARATOR'        => ',',
    'PAYP_PAYPALPLUS_PUI_CURRENCY_THOUSANDS_SEPARATOR'      => ' ',

    'PAYP_PAYPALPLUS_PUI_SUCCESS_DESCRIPTION'               => '<span class="bold">Warum PayPal?</span> Rechnungskauf ist ein Service f&uuml;r den wir mit PayPal zusammenarbeiten. Der Betrag wurde von PayPal soeben direkt an uns gezahlt. Sie bezahlen den Rechnungsbetrag gem&auml;&szlig; den Zahlungshinweisen an PayPal, nachdem Sie die Ware erhalten und gepr&uuml;ft haben.',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_PRINT_INSTRUCTIONS'        => 'Drucken',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_LEGAL_NOTICE'              => '%s hat die Forderung gegen Sie im Rahmen eines laufenden Factoringvertrages an die PayPal (Europe) S.&agrave;r.l. et Cie, S.C.A. abgetreten. Zahlungen mit schuldbefreiender Wirkung k&ouml;nnen nur an die PayPal (Europe) S.&agrave;r.l. et Cie, S.C.A. geleistet werden.',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_TERM'                      => 'Bitte &uuml;berweisen Sie %s bis %s an PayPal.',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER'            => 'Empf&auml;nger',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME'                 => 'Bank',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT'                    => 'Betrag',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER'          => 'Verwendungszweck',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN'                      => 'IBAN',
    'PAYP_PAYPALPLUS_PUI_SUCCESS_BIC'                       => 'BIC',

    'PAYP_PAYPALPLUS_ERR_WALL_PLACEHOLDER_1'                => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sWallPlaceholderId" im Template "payppaypalpluswall.tpl".',
    'PAYP_PAYPALPLUS_ERR_EXT_BUTTON_1'                      => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sExtenalButtonId" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusNextButtonId".',
    'PAYP_PAYPALPLUS_ERR_LOADING_1'                         => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sExtenalButtonId" im Template "payppaypalpluswall.tpl".',
    'PAYP_PAYPALPLUS_ERR_NEXT_LINK_1'                       => 'Das Element oben f&uuml;r den n&auml;chsten Schritt "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sNextStepLink" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusNextLink".',
    'PAYP_PAYPALPLUS_ERR_NEXT_LINK_PARENT_1'                => 'Das Element "%s" (parent of "paypPayPalPlusNextLink") wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sNextStepLinkParent" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusNextLinkParent".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_RADIO_BTN_1'               => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sPaymentRadioButton" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusPaymentRadio".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_DL_1'                      => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sPaymentListItem" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusListItem".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_DT_1'                      => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sPaymentListItemTitle" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusListItemTitle".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_1'                   => 'Das Label der Zahlart "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sPaymentLabelFormat" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusLabelFormat".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_CHILD_1'             => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sPaymentLabelChild" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusLabelChild".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_DESC_1'                    => 'Die Beschreibung der Zahlart "%s" wurde nicht gefunden. Bitte f&uuml;gen Sie diese f&uuml;r alle Sprachen unter "eShop Admin > Shopeinstellungen > Zahlungsarten > Zahlungsart > Beschreibung der Zahlungsart" hinzu. Pr&uuml;fen Sie auch die Variable "sPaymentDescription" (Kind Element von "paypPayPalPlusListItem") im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusDescription" und stellen Sie sicher, dass sich der dort definierte Selektor im Quellcode befindet. diese.',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_1'                  => 'Das Element "%s" wurde im Template nicht gefunden. Pr&uuml;fen Sie die Variable "sPaymentIdPrefix" im Template "payppaypalpluswall.tpl" und den Moduleinstalllungen "paypPayPalPlusMethodIdPrefix".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_2'                  => 'Das Element "%s" wurde im Template nicht gefunden. Check payment is in right containers. Check variable "sPaymentListItem" on "payppaypalpluswall.tpl" und den Moduleinstellungen "paypPayPalPlusListItem". Pr&uuml;fen Sie ebenfalls "sPaymentListItemTitle" on "payppaypalpluswall.tpl" und den Moduleinstellungen "paypPayPalPlusListItemTitle".',
    'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_3'                  => 'Das Element "%s" muss zum Selektor passen welecher unter "paypPayPalPlusPaymentRadio" definiert wurde. Pr&uuml;fen Sie die Variable "sPaymentIdPrefix" im Template "payppaypalpluswall.tpl" und den Moduleinstellungen "paypPayPalPlusMethodIdPrefix". Pr&uuml;fen Sie ebenfalls "sPaymentRadioButton" on "payppaypalpluswall.tpl" und den Moduleinstellungen "paypPayPalPlusPaymentRadio".',
    'PAYP_PAYPALPLUS_SETTINGS_TPL_LOCATION'                 => 'Die Moduleinstellungen finden Sie im Shop Backend unter "eShop Admin > Erweiterungen > Einstellungen > PayPal Plus > PayPal Plus Integration Settings"',
    'PAYP_PAYPALPLUS_ERROR_NO_PAYMENT_FOUND_FOR_EVENT'      => 'Fehler: Zu diesem Event sind im Shop keine zugeh&ouml;rigen Zahlungsinformationen hinterlegt',
    'PAYP_PAYPALPLUS_ERROR_PAYMENT_DATA_NOT_SAVED'          => 'Fehler: Die Payment Daten konnten nicht gespeichert oder aktualisiert werden',
    'PAYP_PAYPALPLUS_ERROR_PAYMENT_NOT_VALID'               => 'Fehler: Das Payment konnte nicht ausgef&uuml;hrt werden. Payment->execute hat einen invaliden Status zur&uuml;ckgegeben',
    'PAYP_PAYPALPLUS_ERROR_NO_USER_CREDENTIALS'             => 'Hinweis: Bitte editieren Sie die API Einstellungen im Tab "Einstellungen" um die PayPal Webhooks nutzen zu k&ouml;nnen. Deaktiviern Sie dannach das Modul und aktivieren Sie es wieder.',
    'PAYP_PAYPALPLUS_ERROR_PAYPAL_ERROR_OR_SESSION_EXPIRED' => 'Der PayPal Service hat einen Fehler zur&uuml;ckgegeben. Bitte versuchen Sie einen erneuten Checkout &uuml;ber PayPal oder w&auml;hlen Sie eine andere Zahlungsart',
    'PAYP_PAYPALPLUS_TEST'                                  => 'german string with &uuml;'
);
