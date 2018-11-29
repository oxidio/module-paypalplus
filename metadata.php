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

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'payppaypalplus',
    'title'       => 'PayPal Plus',
    'description' => array(
        'de' => 'PayPal Plus Bezahlmodul für OXID eShop',
        'en' => 'PayPal Plus payments module for OXID eShop',
    ),
    'thumbnail'   => 'out/pictures/payppaypalplus.png',
    'version'     => '3.0.3',
    'author'      => 'PayPal (Europe) S.à r.l. et Cie, S.C.A.',
    'url'         => 'https://www.paypal.com',
    'email'       => 'service@paypal.com',
    'extend'      => array(
        'language_main'    => 'payp/paypalplus/controllers/admin/admin_payppaypalpluslanguage_main',
        'order_list'       => 'payp/paypalplus/controllers/admin/admin_payppaypalplusorder_list',
        'module_config'    => 'payp/paypalplus/controllers/admin/admin_payppaypalplusmodule_config',
        'basket'           => 'payp/paypalplus/controllers/payppaypalplusbasket',
        'order'            => 'payp/paypalplus/controllers/payppaypalplusorder',
        'payment'          => 'payp/paypalplus/controllers/payppaypalpluspayment',
        'oxviewconfig'     => 'payp/paypalplus/core/payppaypalplusoxviewconfig',
        'oxaddress'        => 'payp/paypalplus/models/payppaypalplusoxaddress',
        'oxbasket'         => 'payp/paypalplus/models/payppaypalplusoxbasket',
        'oxorder'          => 'payp/paypalplus/models/payppaypalplusoxorder',
        'oxpaymentgateway' => 'payp/paypalplus/models/payppaypalplusoxpaymentgateway',
        'oxuser'           => 'payp/paypalplus/models/payppaypalplusoxuser',
        'thankyou'         => 'payp/paypalplus/controllers/payppaypalplusthankyou',
    ),
    'files'       => array(
        'payppaypalpluswall'                                 => 'payp/paypalplus/components/widgets/payppaypalpluswall.php',
        'admin_payppaypalplusordertab'                       => 'payp/paypalplus/controllers/admin/admin_payppaypalplusordertab.php',
        'payppaypalplusnoorderexception'                     => 'payp/paypalplus/core/exception/payppaypalplusnoorderexception.php',
        'payppaypalplusnopaymentfoundexception'              => 'payp/paypalplus/core/exception/payppaypalplusnopaymentfoundexception.php',
        'payppaypalplusrefundexception'                      => 'payp/paypalplus/core/exception/payppaypalplusrefundexception.php',
        'payppaypalplusconfig'                               => 'payp/paypalplus/core/payppaypalplusconfig.php',
        'payppaypalplusdataaccess'                           => 'payp/paypalplus/core/payppaypalplusdataaccess.php',
        'payppaypalplusdataconverter'                        => 'payp/paypalplus/core/payppaypalplusdataconverter.php',
        'payppaypalpluserrorhandler'                         => 'payp/paypalplus/core/payppaypalpluserrorhandler.php',
        'payppaypalplusevents'                               => 'payp/paypalplus/core/payppaypalplusevents.php',
        'payppaypalplusinvoicepdfarticlesummary'             => 'payp/paypalplus/core/payppaypalplusinvoicepdfarticlesummary.php',
        'payppaypalpluspdfarticlesummarypaymentinstructions' => 'payp/paypalplus/core/payppaypalpluspdfarticlesummarypaymentinstructions.php',
        'payppaypalplusmodule'                               => 'payp/paypalplus/core/payppaypalplusmodule.php',
        'payppaypalpluspaymenthandler'                       => 'payp/paypalplus/core/payppaypalpluspaymenthandler.php',
        'payppaypalpluspdfarticlesummary'                    => 'payp/paypalplus/core/payppaypalpluspdfarticlesummary.php',
        'payppaypalplusrefundhandler'                        => 'payp/paypalplus/core/payppaypalplusrefundhandler.php',
        'payppaypalpluswebprofilehandler'                    => 'payp/paypalplus/core/payppaypalpluswebprofilehandler.php',
        'payppaypalplussdk'                                  => 'payp/paypalplus/core/payppaypalplussdk.php',
        'payppaypalplussession'                              => 'payp/paypalplus/core/payppaypalplussession.php',
        'payppaypalplusshop'                                 => 'payp/paypalplus/core/payppaypalplusshop.php',
        'payppaypalplussupercfg'                             => 'payp/paypalplus/core/payppaypalplussupercfg.php',
        'payppaypalplustaxationhandler'                      => 'payp/paypalplus/core/payppaypalplustaxationhandler.php',
        'payppaypalplusvalidator'                            => 'payp/paypalplus/core/payppaypalplusvalidator.php',
        'payppaypalplusbasketdata'                           => 'payp/paypalplus/models/payppaypalplusbasketdata.php',
        'payppaypalplusbasketitemdata'                       => 'payp/paypalplus/models/payppaypalplusbasketitemdata.php',
        'payppaypalplusdataprovider'                         => 'payp/paypalplus/models/payppaypalplusdataprovider.php',
        'payppaypalpluspaymentdata'                          => 'payp/paypalplus/models/payppaypalpluspaymentdata.php',
        'payppaypalpluspaymentdataprovider'                  => 'payp/paypalplus/models/payppaypalpluspaymentdataprovider.php',
        'payppaypalplusprofile'                              => 'payp/paypalplus/models/payppaypalplusprofile.php',
        'payppaypalpluspuidata'                              => 'payp/paypalplus/models/payppaypalpluspuidata.php',
        'payppaypalpluspuidataprovider'                      => 'payp/paypalplus/models/payppaypalpluspuidataprovider.php',
        'payppaypalplusrefunddata'                           => 'payp/paypalplus/models/payppaypalplusrefunddata.php',
        'payppaypalplusrefunddatalist'                       => 'payp/paypalplus/models/payppaypalplusrefunddatalist.php',
        'payppaypalplusrefunddataprovider'                   => 'payp/paypalplus/models/payppaypalplusrefunddataprovider.php',
        'payppaypalplususerdata'                             => 'payp/paypalplus/models/payppaypalplususerdata.php',
        'payppaypalpluswebhook'                              => 'payp/paypalplus/controllers/payppaypalpluswebhook.php',
    ),
    'templates'   => array(
        'payppaypalpluswall.tpl'  => 'payp/paypalplus/views/widgets/payppaypalpluswall.tpl',
        'payppaypalplusorder.tpl' => 'payp/paypalplus/views/admin/tpl/payppaypalplusorder.tpl',
        'page/webhook/response.tpl' => 'payp/paypalplus/views/tpl/page/webhook/response.tpl',
    ),
    'blocks'      => array(
        array(
            'template' => 'page/checkout/inc/payment_other.tpl',
            'block'    => 'checkout_payment_longdesc',
            'file'     => 'views/blocks/payppaypalplus_payment_description.tpl',
        ),
        array(
            'template' => 'page/checkout/thankyou.tpl',
            'block'    => 'checkout_thankyou_info',
            'file'     => 'views/blocks/payppaypalplus_checkout_thankyou_info.tpl',
        ),
        array(
            'template' => 'page/checkout/order.tpl',
            'block'    => 'shippingAndPayment',
            'file'     => 'views/blocks/payppaypalplus_order_payment.tpl',
        ),
        array(
            'template' => 'email/html/order_cust.tpl',
            'block'    => 'email_html_order_cust_orderemailend',
            'file'     => 'views/blocks/payppaypalplus_email_html_order_cust_orderemailend.tpl',
        ),
        array(
            'template' => 'email/plain/order_cust.tpl',
            'block'    => 'email_plain_order_cust_orderemailend',
            'file'     => 'views/blocks/payppaypalplus_email_plain_order_cust_orderemailend.tpl',
        ),
        array(
            'template' => 'language_main.tpl',
            'block'    => 'admin_language_main_form',
            'file'     => 'views/blocks/payppaypalplus_admin_language_main_form.tpl',
        ),
        array(
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_filter',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_filter_actions.tpl'),
        array(
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_sorting',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_sorting_actions.tpl'),
        array(
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_item',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_items_actions.tpl'),
        array(
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_colgroup',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_colgroup_actions.tpl'),
        array(
            'template' => 'module_config.tpl',
            'block'    => 'admin_module_config_form',
            'file'     => '/views/blocks/paypalplus_admin_module_config_form.tpl'),
    ),
    'settings'    => array(
        array(
            'group' => 'paypPayPalPlusApi',
            'name'  => 'paypPayPalPlusClientId',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusApi',
            'name'  => 'paypPayPalPlusSecret',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusSandbox',
            'name'  => 'paypPayPalPlusSandbox',
            'type'  => 'bool',
            'value' => false,
        ),
        array(
            'group' => 'paypPayPalPlusSandbox',
            'name'  => 'paypPayPalPlusSandboxClientId',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusSandbox',
            'name'  => 'paypPayPalPlusSandboxSecret',
            'type'  => 'str',
            'value' => '',
        ),

        /** Common integration settings **/

        array(
            'group' => 'paypPayPalPlusIntegration',
            'name'  => 'paypPayPalPlusExternalMethods',
            'type'  => 'arr',
            'value' => array('oxidinvoice', 'oxidpayadvance', 'oxidcashondel', 'oxempty'),
        ),
        array(
            'group' => 'paypPayPalPlusIntegration',
            'name'  => 'paypPayPalPlusValidateTemplate',
            'type'  => 'bool',
            'value' => true,
        ),

        /** Settings for template integration **/

        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusNextButtonId',
            'type'  => 'str',
            'value' => 'paymentNextStepBottom',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusNextLink',
            'type'  => 'str',
            'value' => 'a#orderStep',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusNextLinkParent',
            'type'  => 'str',
            'value' => 'span',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusPaymentRadio',
            'type'  => 'str',
            'value' => 'input[name="paymentid"]',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusListItem',
            'type'  => 'str',
            'value' => 'dl',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusListItemTitle',
            'type'  => 'str',
            'value' => 'dt',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusLabelFormat',
            'type'  => 'str',
            'value' => 'label[for="payment_%s"]',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusLabelChild',
            'type'  => 'str',
            'value' => 'b',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusDescription',
            'type'  => 'str',
            'value' => 'div.desc',
        ),
        array(
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusMethodIdPrefix',
            'type'  => 'str',
            'value' => 'payment_',
        ),

        /** Settings for Mobile template integration **/

        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobNextButtonId',
            'type'  => 'str',
            'value' => 'paymentNextStepBottom',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobNextLink',
            'type'  => 'str',
            'value' => 'a#orderStep',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobNextLinkParent',
            'type'  => 'str',
            'value' => 'li',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobPaymentRadio',
            'type'  => 'str',
            'value' => 'input[name="paymentid"]',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobListItem',
            'type'  => 'str',
            'value' => '#paymentMethods ul.dropdown-menu li',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobListItemTitle',
            'type'  => 'str',
            'value' => 'a',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobLabelFormat',
            'type'  => 'str',
            'value' => 'a[data-selection-id="%s"]',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobDescription',
            'type'  => 'str',
            'value' => 'div[id="paymentOption_%s"] div.payment-desc',
        ),
        array(
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobMethodIdPrefix',
            'type'  => 'str',
            'value' => 'payment_',
        ),

        /** Settings for Flow template integration **/

        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowNextButtonId',
            'type'  => 'str',
            'value' => 'paymentNextStepBottom',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowNextLink',
            'type'  => 'str',
            'value' => 'a#orderStep',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowNextLinkParent',
            'type'  => 'str',
            'value' => 'li',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowPaymentRadio',
            'type'  => 'str',
            'value' => 'input[name="paymentid"]',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowListItem',
            'type'  => 'str',
            'value' => '.panel-body .well',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowListItemTitle',
            'type'  => 'str',
            'value' => 'dt',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowLabelFormat',
            'type'  => 'str',
            'value' => 'label[for="payment_%s"]',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowDescription',
            'type'  => 'str',
            'value' => 'div.desc',
        ),
        array(
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowMethodIdPrefix',
            'type'  => 'str',
            'value' => 'payment_',
        ),

        /** Logging debugging and connectivity **/

        array(
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusLogEnabled',
            'type'  => 'bool',
            'value' => true,
        ),
        array(
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusLogFile',
            'type'  => 'str',
            'value' => 'paypalplus.log',
        ),
        array(
            'group'      => 'paypPayPalPlusOther',
            'name'       => 'paypPayPalPlusLogLevel',
            'type'       => 'select',
            'constrains' => 'DEBUG|INFO|WARN|ERROR',
            'value'      => 'INFO',
        ),
        array(
            'group'      => 'paypPayPalPlusOther',
            'name'       => 'paypPayPalPlusValidation',
            'type'       => 'select',
            'constrains' => 'log|strict|disabled',
            'value'      => 'log',
        ),
        array(
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusTimeout',
            'type'  => 'num',
            'value' => 60,
        ),
        array(
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusRetry',
            'type'  => 'num',
            'value' => 1,
        ),
        array(
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusDebug',
            'type'  => 'bool',
            'value' => false,
        ),
        array(
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusSaveToFile',
            'type'  => 'bool',
            'value' => false,
        ),

        /** Other settings **/

        array(
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusDiscountRefunds',
            'type'  => 'bool',
            'value' => true,
        ),
        array(
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusRefundOnInvoice',
            'type'  => 'bool',
            'value' => false,
        ),
        array(
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusShopOwnerStr',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusInvNr',
            'type'  => 'bool',
            'value' => false,
        ),

        /** Paypal Payment Experience settings **/

        array(
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpProfileId',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpName',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpBrand',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpLogo',
            'type'  => 'str',
            'value' => '',
        ),
        array(
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpLocale',
            'type'  => 'str',
            'value' => '',
        ),
    ),
    'events'      => array(
        'onActivate'   => 'paypPayPalPlusModule::onActivate',
        'onDeactivate' => 'paypPayPalPlusModule::onDeactivate',
    ),
);