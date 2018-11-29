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
    'charset'                                          => 'UTF-8',
    'tbclorder_paypalplus'                             => 'PayPal Plus',

    // Settings interface translations
    'SHOP_MODULE_GROUP_paypPayPalPlusApi'              => 'API Einstellungen',
    'SHOP_MODULE_paypPayPalPlusClientId'               => 'Benutzer ID',
    'SHOP_MODULE_paypPayPalPlusSecret'                 => 'Schl&uuml;ssel',

    'SHOP_MODULE_GROUP_paypPayPalPlusSandbox'          => 'Sandbox API Einstellungen',
    'SHOP_MODULE_paypPayPalPlusSandbox'                => 'Sandbox Modus',
    'SHOP_MODULE_paypPayPalPlusSandboxClientId'        => 'Sandbox Benutzer ID',
    'SHOP_MODULE_paypPayPalPlusSandboxSecret'          => 'Sandbox Schl&uuml;ssel',

    'SHOP_MODULE_GROUP_paypPayPalPlusIntegration'      => 'PayPal Plus Integrationseinstellungen',
    'SHOP_MODULE_paypPayPalPlusExternalMethods'        => 'IDs der Zahlungsmethoden, welche innerhalb des PayPal Plus widgets angezeigt werden sollen.<br/>' .
                                                          'Status und Bestellsortierung werden in <i>Shopeinstellungen -> Zahlungsarten</i> konfiguriert.<br/>' .
                                                          'Zu Beachten ist, dass die OXID Methoden "oxiddebitnote" und "oxidcreditcard" nicht in der Payment wall angezeigt werden k&ouml;nnen.<br/>' .
                                                          'Es k&ouml;nnen maximal 5 Zahlungsmethoden hinzugef&uuml;gt werden.',

    'SHOP_MODULE_GROUP_paypPayPalPlusTemplateIntegration'    => 'PayPal Plus Temmplateeinstellungen',
    'SHOP_MODULE_paypPayPalPlusNextButtonId'           => '* [paypPayPalPlusNextButtonId] Die ID des "Weiter"-Buttons auf der Bezahlseite.',
    'SHOP_MODULE_paypPayPalPlusNextLink'               => '[paypPayPalPlusNextLink] CSS Selektor f&uuml;r die obere Navigation "Weiter" auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusNextLinkParent'         => '[paypPayPalPlusNextLinkParent] CSS Selektor f&uuml;r das Elternelement der oberen Navigation "Weiter" auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusPaymentRadio'           => '* [paypPayPalPlusPaymentRadio] CSS Selektor f&uuml;r die Radiobuttons (Auswahl der Bezahlmethode) auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusListItem'               => '* [paypPayPalPlusListItem] CSS Selektor f&uuml;r die Listeneintr&auml;ge auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusListItemTitle'          => '[paypPayPalPlusListItemTitle] CSS Selektor f&uuml;r das Titel Element der Bezahlmethoden auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusLabelFormat'            => '[paypPayPalPlusLabelFormat] CSS Format f&uuml;r das Label "Zahlungsart" auf der Seite "Bezahlen" im Checkout.' .
                                                          'Falls "%s" genutzt wird, wird diese mit der ID der Zahlungsart ausgetauscht.',
    'SHOP_MODULE_paypPayPalPlusLabelChild'             => '[paypPayPalPlusLabelChild] CSS Selektor f&uuml;r das Kindelement des Labels Zahlungsart ("paypPayPalPlusLabelFormat") auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusDescription'            => '[paypPayPalPlusDescription] CSS Selektor f&uuml;r das Beschreibungstag der Bezahlart im Checkout',
    'SHOP_MODULE_paypPayPalPlusMethodIdPrefix'         => '[paypPayPalPlusMethodIdPrefix] Pr&auml;fix f&uuml;r die ID des Attributs "Zahlungsart" welches f&uuml;r Eingabefelder auf der Seite "Bezahlen" im Checkout genutzt wird.',

    'SHOP_MODULE_GROUP_paypPayPalPlusMobIntegration'      => 'PayPal Plus Mobile Templateeinstellungen',
    'SHOP_MODULE_paypPayPalPlusMobNextButtonId'           => '* [paypPayPalPlusNextButtonId] Die ID des "Weiter"-Buttons auf der Bezahlseite.',
    'SHOP_MODULE_paypPayPalPlusMobNextLink'               => '[paypPayPalPlusNextLink] CSS Selektor f&uuml;r die obere Navigation "Weiter" auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusMobNextLinkParent'         => '[paypPayPalPlusNextLinkParent] CSS Selektor f&uuml;r das Elternelement der oberen Navigation "Weiter" auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusMobPaymentRadio'           => '* [paypPayPalPlusPaymentRadio] CSS Selektor f&uuml;r die Radiobuttons (Auswahl der Bezahlmethode) auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusMobListItem'               => '* [paypPayPalPlusListItem] CSS Selektor f&uuml;r die Listeneintr&auml;ge auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusMobListItemTitle'          => '[paypPayPalPlusListItemTitle] CSS Selektor f&uuml;r das Titel Element der Bezahlmethoden auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusMobLabelFormat'            => '[paypPayPalPlusLabelFormat] CSS Format f&uuml;r das Label "Zahlungsart" auf der Seite "Bezahlen" im Checkout.' .
        'Falls "%s" genutzt wird, wird diese mit der ID der Zahlungsart ausgetauscht.',
    'SHOP_MODULE_paypPayPalPlusMobLabelChild'             => '[paypPayPalPlusLabelChild] CSS Selektor f&uuml;r das Kindelement des Labels Zahlungsart ("paypPayPalPlusLabelFormat") auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusMobDescription'            => '[paypPayPalPlusDescription] CSS Selektor f&uuml;r das Beschreibungstag der Bezahlart im Checkout',
    'SHOP_MODULE_paypPayPalPlusMobMethodIdPrefix'         => '[paypPayPalPlusMethodIdPrefix] Pr&auml;fix f&uuml;r die ID des Attributs "Zahlungsart" welches f&uuml;r Eingabefelder auf der Seite "Bezahlen" im Checkout genutzt wird.',

    'SHOP_MODULE_GROUP_paypPayPalPlusFlowIntegration'      => 'PayPal Plus Flow Templateeinstellungen',
    'SHOP_MODULE_paypPayPalPlusFlowNextButtonId'           => '* [paypPayPalPlusNextButtonId] Die ID des "Weiter"-Buttons auf der Bezahlseite.',
    'SHOP_MODULE_paypPayPalPlusFlowNextLink'               => '[paypPayPalPlusNextLink] CSS Selektor f&uuml;r die obere Navigation "Weiter" auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusFlowNextLinkParent'         => '[paypPayPalPlusNextLinkParent] CSS Selektor f&uuml;r das Elternelement der oberen Navigation "Weiter" auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusFlowPaymentRadio'           => '* [paypPayPalPlusPaymentRadio] CSS Selektor f&uuml;r die Radiobuttons (Auswahl der Bezahlmethode) auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusFlowListItem'               => '* [paypPayPalPlusListItem] CSS Selektor f&uuml;r die Listeneintr&auml;ge auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusFlowListItemTitle'          => '[paypPayPalPlusListItemTitle] CSS Selektor f&uuml;r das Titel Element der Bezahlmethoden auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusFlowLabelFormat'            => '[paypPayPalPlusLabelFormat] CSS Format f&uuml;r das Label "Zahlungsart" auf der Seite "Bezahlen" im Checkout.' .
        'Falls "%s" genutzt wird, wird diese mit der ID der Zahlungsart ausgetauscht.',
    'SHOP_MODULE_paypPayPalPlusFlowLabelChild'             => '[paypPayPalPlusLabelChild] CSS Selektor f&uuml;r das Kindelement des Labels Zahlungsart ("paypPayPalPlusLabelFormat") auf der Seite "Bezahlen" im Checkout.',
    'SHOP_MODULE_paypPayPalPlusFlowDescription'            => '[paypPayPalPlusDescription] CSS Selektor f&uuml;r das Beschreibungstag der Bezahlart im Checkout',
    'SHOP_MODULE_paypPayPalPlusFlowMethodIdPrefix'         => '[paypPayPalPlusMethodIdPrefix] Pr&auml;fix f&uuml;r die ID des Attributs "Zahlungsart" welches f&uuml;r Eingabefelder auf der Seite "Bezahlen" im Checkout genutzt wird.',

    'SHOP_MODULE_paypPayPalPlusValidateTemplate'       => 'Template Validierung durchf&uuml;hren',
    'SHOP_MODULE_paypPayPalPlusInvNr'                  => 'Die Bestellnummer als "Rechnungsnummer" an PayPal &uuml;bertragen. Kann zu L&uuml;cken im Bestellnummer Nummernkreis f&uuml;hren.',
    'HELP_SHOP_MODULE_paypPayPalPlusInvNr'             => 'Bedingt durch den Aufbau der PayPal Schnittstelle wird die Bestellnummer schon bei Auswahl der Zahlungsart reserviert und nicht wie sonst &uuml;blich w&auml;hrend des Bestellabschlusses. '.
                                                          'Wenn die Bestellung nicht abgeschlossen wird, wird die Nummer nicht einer anderen Bestellung zugewiesen und kommt es zu einer L&uuml;cke im Nummernkreis.',

    'SHOP_MODULE_GROUP_paypPayPalPlusOther'            => 'Logger, Debugger und Verbindungseinstellungen',
    'SHOP_MODULE_paypPayPalPlusLogEnabled'             => 'Daten der PayPal API in eine Datei speichern',
    'SHOP_MODULE_paypPayPalPlusLogFile'                => 'PayPal API Logdateiname innerhalb des eShop log/ Ordners',
    'SHOP_MODULE_paypPayPalPlusLogLevel'               => 'PayPal API Log-Level',
    'SHOP_MODULE_paypPayPalPlusLogLevel_DEBUG'         => 'DEBUG (Nur im SANDBOX Modus verwenden!)',
    'SHOP_MODULE_paypPayPalPlusLogLevel_INFO'          => 'INFO',
    'SHOP_MODULE_paypPayPalPlusLogLevel_WARN'          => 'WARN',
    'SHOP_MODULE_paypPayPalPlusLogLevel_ERROR'         => 'ERROR',
    'SHOP_MODULE_paypPayPalPlusValidation'             => 'PayPal API Datenvalidierungslevel',
    'SHOP_MODULE_paypPayPalPlusValidation_log'         => 'log',
    'SHOP_MODULE_paypPayPalPlusValidation_strict'      => 'strict',
    'SHOP_MODULE_paypPayPalPlusValidation_disabled'    => 'disabled',
    'SHOP_MODULE_paypPayPalPlusTimeout'                => 'PayPal API Connection Timeout in Sekunden',
    'SHOP_MODULE_paypPayPalPlusRetry'                  => 'Versuche zum Aufbauen einer PayPal Verbindung',
    'SHOP_MODULE_paypPayPalPlusDebug'                  => 'PayPal API Exeptions im Frontend anzeigen (nur bei Debug Modus)',
    'SHOP_MODULE_paypPayPalPlusSaveToFile'             => 'Speichern der ausgef&uuml;hrten Zahlungen in einer Textdatei des eShop log/ Ordners',
    'SHOP_MODULE_paypPayPalPlusPhoneInternationalOnly' => 'Nur das internationale Telefonformat nach E.123 zulassen (startet mit "+") sonst gilt E.123',

    'SHOP_MODULE_GROUP_paypPayPalPlusPUI'              => 'Weitere Einstellungen',
    'SHOP_MODULE_paypPayPalPlusShopOwnerStr'           => 'Name des Shop-Betreibers auf "PayPal Rechnungskauf" Rechnungen',
    'HELP_SHOP_MODULE_paypPayPalPlusShopOwnerStr'      => 'Name des Shop-Betreibers wie er im rechtlichen Hinweis auf den Rechnungen angegeben ist, wenn "PayPal Rechnungskauf" als Zahlungsart ausgew&auml;hlt wurde. Wenn hier nichts angegeben wird, z&auml;hlt die Einstellung in den Grundeinstellungen des Shops.',
    'SHOP_MODULE_paypPayPalPlusDiscountRefunds'        => 'PayPal Plus R&uuml;ckzahlungen sollen als Rabatte in die Bestellungen &uuml;bernommen werden.',
    'HELP_SHOP_MODULE_paypPayPalPlusDiscountRefunds'   => 'Diese Einstellung ist f&uuml;r die PayPal Plus Zahlungsmethode "PayPal Rechnungskauf" wichtig, ' .
                                                          'da hier die Rechnungen vom Shop-Betreiber generiert werden m&uuml;ssen und der Rechnungsendbetrag dem zu zahlenden Betrag entsprechen muss',

    'SHOP_MODULE_paypPayPalPlusRefundOnInvoice'        => 'PayPal Plus R&uuml;ckzahlungen sollen nach Rechnungsdruck nicht mehr m&ouml;glich sein.',
    'HELP_SHOP_MODULE_paypPayPalPlusRefundOnInvoice'   => 'Wenn die Rechnungen ausserhalb des OXID eShop erzeugt werden, m&uuml;ssen die Felder  OXBILLNR bzw. OXINVOICENR in der Tabelle oxorders nach Rechnungserzeugung gesetzt werden, damit vom Shop gepr&uuml;ft werden kann, ob eine Rechnung zu der Bestellung existiert.',
    'SHOP_MODULE_paypPayPalPlusEstimatedDeliveryDate'  => 'Lieferzeit der Artikel im Warenkorb an PayPal übermitteln',


    // Order tab "PayPal Plus" translations
    'PAYP_PAYPALPLUS_ONLY_FOR_PAYPAL_PLUS_PAYMENT'     => 'Diese Registerkarte ist nur f&uuml;r Bestellungen mit der Zahlungsart PayPal Plus.',

    'PAYP_PAYPALPLUS_PAYMENT_OVERVIEW'                 => 'Zahlungs&uuml;bersicht',
    'PAYP_PAYPALPLUS_PAYMENT_STATUS'                   => 'Zahlungsstatus',
    'PAYP_PAYPALPLUS_ORDER_AMOUNT'                     => 'Bestellpreis gesamt',
    'PAYP_PAYPALPLUS_REFUNDED_AMOUNT'                  => 'Erstatteter Betrag',
    'PAYP_PAYPALPLUS_PAYMENT_ID'                       => 'Bezahlung-ID',
    'PAYP_PAYPALPLUS_PAYMENT_METHOD'                   => 'Zahlungsart',

    'PAYP_PAYPALPLUS_PAYMENT_REFUNDING'                => 'R&uuml;ckerstattungen',
    'PAYP_PAYPALPLUS_AVAILABLE_REFUNDS'                => 'Anzahl verf&uuml;gbarer R&uuml;ckerstattungen:',
    'PAYP_PAYPALPLUS_AVAILABLE_REFUND_AMOUNT'          => 'Verf&uuml;gbarer R&uuml;ckerstattungsbetrag:',
    'PAYP_PAYPALPLUS_DATE'                             => 'Datum',
    'PAYP_PAYPALPLUS_AMOUNT'                           => 'Betrag',
    'PAYP_PAYPALPLUS_STATUS'                           => 'Status',
    'PAYP_PAYPALPLUS_NEW_REFUND'                       => 'R&uuml;ckerstattung veranlassen',
    'PAYP_PAYPALPLUS_REFUND'                           => 'Erstatten',

    'PAYP_PAYPALPLUS_PUI'                              => 'Rechnungskauf',
    'PAYP_PAYPALPLUS_PUI_PAYMENT_INSTRUCTIONS'         => 'Zahlungshinweise',
    'PAYP_PAYPALPLUS_PUI_TERM'                         => 'Zahlungsziel',
    'PAYP_PAYPALPLUS_PUI_ACCOUNT_HOLDER'               => 'Empf&auml;nger',
    'PAYP_PAYPALPLUS_PUI_BANK_NAME'                    => 'Bank',
    'PAYP_PAYPALPLUS_PUI_AMOUNT'                       => 'Betrag',
    'PAYP_PAYPALPLUS_PUI_REFERENCE_NUMBER'             => 'Verwendungszweck',
    'PAYP_PAYPALPLUS_PUI_IBAN'                         => 'IBAN',
    'PAYP_PAYPALPLUS_PUI_BIC'                          => 'BIC',

    /** Admin order list */
    'PAYP_PAYPALPLUS_LIST_STATUS_ALL'                  => 'Alle',

    /** Error messages */
    'PAYP_PAYPALPLUS_ERR_INVALID_REQUEST'              => 'Ung&uuml;ltige Anfrage! Bitte aktualisieren Sie die Seite und versuchen Sie es erneut.',
    'PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT'               => 'Es k&ouml;nnen ausschlie&szlig;lich positive nummerische Werte eingegeben werden',
    'PAYP_PAYPALPLUS_ERR_REFUND_NOT_POSSIBLE'          => 'Die maximale Anzahl an R&uuml;ckerstattungen ist erreicht',
    'PAYP_PAYPALPLUS_ERR_REFUND_API_EXCEPTION'         => 'Die R&uuml;ckerstattung konnte nicht durchgef&uuml;hrt werden! Bitte versuchen Sie es erneut oder kontaktieren Sie Ihren Shopadministrator.',
    'PAYP_PAYPALPLUS_ERR_PAYMENTSTATUS_NOT_FOUND'      => 'Es wurde keine zugeh&ouml;ige PayPal Plus Zahlung gefunden. Bitte stellen Sie sicher, dass die Zahlung wirklich per PayPal Plus get&auml;tigt wurde.',
    'PAYP_PAYPALPLUS_ERR_PAYMENTSTATUS_NOT_UPDATED'    => 'Der Status des zugeh&ouml;igen PayPal Plus Zahlung konnte nicht auf "completed" gesetzt werden.',
    'PAYP_PAYPALPLUS_ERR_INVOICE_UPDATE_REQUIRED'      => 'Eine R&uuml;ckerstattung wurde durchgef&uuml;hrt, bitte aktualisieren Sie die dazugeh&ouml;rige Rechnung',
    'PAYP_PAYPALPLUS_ERR_INVOICE_EXISTS'               => 'Eine R&uuml;ckerstattung kann nicht durchgef&uuml;hrt werden, da f&uuml;r diese Bestellung bereits eine Rechnung generiert wurde.',

    'PAYP_PAYPALPLUS_COUNTRY_LOCALE_CODE'              => 'PayPal Plus Sprachk&uuml;rzel',
    'HELP_PAYP_PAYPALPLUS_COUNTRY_LOCALE_CODE'         => 'Eine Liste aller f&uuml;r Ihr Land g&uuml;ltigen Sprachk&uuml;rzel finden Sie unter https://developer.paypal.com/docs/classic/api/locale_codes/',

    'SHOP_MODULE_GROUP_paypPayPalPlusExperience'       => 'PayPal Experience Profile Einstellungen',
    'SHOP_MODULE_paypPayPalPlusExpProfileId'           => 'Experience Profile Id. Wenn Sie bereits über eine ID verf&uuml;gen kann diese hier eingetragen werden, falls nicht wird diese automatisch bef&uuml;llt.',
    'SHOP_MODULE_paypPayPalPlusExpName'                => '* Name des Experience Profils',
    'SHOP_MODULE_paypPayPalPlusExpBrand'               => 'Bezeichnung des Shops welche auf den PayPal Seiten angezeigt wird.',
    'SHOP_MODULE_paypPayPalPlusExpLogo'                => 'Https Url zu einem Logo. Erlaubte Werte: .gif, .jpg, or .png',
    'SHOP_MODULE_paypPayPalPlusExpLocale'              => 'Locale Code des Profils (de,us,...)',

    'PAYP_PAYPALPLUS_ERR_EXP_NAME_EMPTY'               => 'Name of the web experience profile cannot be empty[/DE]',
    'PAYP_PAYPALPLUS_ERROR_NO_USER_CREDENTIALS'        => 'Hinweis: Bitte editieren Sie die API Einstellungen im Tab "Einstellungen" um die PayPal Webhooks nutzen zu k&ouml;nnen. Deaktiviern Sie dannach das Modul und aktivieren Sie es wieder.',
    'PAYP_PAYPALPLUS_ERROR_NOSSL_URL'                  => 'Hinweis: Bitte konfigurieren Sie eine SSL Url in der Datei config.inc.php um PayPal Webhooks verwenden zu k&ouml;nnen. Deaktiviern Sie dannach das Modul und aktivieren Sie es wieder.',
);
