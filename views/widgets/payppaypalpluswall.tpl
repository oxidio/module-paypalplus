[{assign var="sWallPlaceholderId" value="paypPayPalPlusWall"}]
[{assign var="sLoadingIndicatorId" value="paypPayPalPlusLoading"}]
[{assign var="sExtenalButtonId" value=$oView->getExternalButtonId()}]
[{assign var="sNextStepLink" value=$oView->getNextStepLink()}]
[{assign var="sNextStepLinkParent" value=$oView->getNextStepLinkParent()}]
[{assign var="sPaymentRadioButton" value=$oView->getPaymentRadioButton()}]
[{assign var="sPaymentListItem" value=$oView->getPaymentListItem()}]
[{assign var="sPaymentListItemTitle" value=$oView->getPaymentListItemTitle()}]
[{assign var="sPaymentLabelFormat" value=$oView->getPaymentLabelFormat()}]
[{assign var="sPaymentLabelChild" value=$oView->getPaymentLabelChild()}]
[{assign var="sPaymentDescription" value=$oView->getPaymentDescription()}]
[{assign var="sPaymentIdPrefix" value=$oView->getPaymentIdPrefix()}]
[{assign var="sPayPalPlusMethodId" value=$oViewConf->getPayPalPlusMethodId()}]
[{assign var="sApprovalUrl" value=$oView->getApprovalUrl()}]
[{assign var="sApiMode" value=$oView->getApiMode()}]
[{assign var="sLanguage" value=$oView->getLanguageCode()}]
[{assign var="sCountry" value=$oView->getCountryCode()}]
[{assign var="sAjaxResponseToken" value=$oView->getAjaxResponseToken()}]
[{assign var="sErrorMessage" value=$oView->getErrorMessage()}]
[{assign var="sGeneralErrorCode" value=$oView->getGeneralErrorCode()}]
[{assign var="sExternalMethods" value=$oView->getExternalMethods()|@json_encode}]
[{assign var="sExternalMethodRedirectUrl"       value=$oView->getExternalMethodsRedirectUrl()}]
[{assign var="blTemplateValidationNeeded"       value=$oView->isTemplateValidationNeeded()}]
[{assign var="blMobile"                         value=$oView->isMobile()}]
[{assign var="blFlow"                           value=$oView->isFlow()}]
[{assign var="sError_domWallPlaceholderId_1"    value="PAYP_PAYPALPLUS_ERR_WALL_PLACEHOLDER_1"|oxmultilangassign}]
[{assign var="sError_domExternalButtonId_1"     value="PAYP_PAYPALPLUS_ERR_EXT_BUTTON_1"|oxmultilangassign:$sExtenalButtonId}]
[{assign var="sError_domLoadingIndicatorId_1"   value="PAYP_PAYPALPLUS_ERR_LOADING_1"|oxmultilangassign}]
[{assign var="sError_domNextStepLink_1"         value="PAYP_PAYPALPLUS_ERR_NEXT_LINK_1"|oxmultilangassign:$sNextStepLink}]
[{assign var="sError_domNextStepLinkParent_1"   value="PAYP_PAYPALPLUS_ERR_NEXT_LINK_PARENT_1"|oxmultilangassign}]
[{assign var="sError_domPaymentRadioButton_1"   value="PAYP_PAYPALPLUS_ERR_PAYMENT_RADIO_BTN_1"|oxmultilangassign:$sPaymentRadioButton}]
[{assign var="sError_domPaymentListItem_1"      value="PAYP_PAYPALPLUS_ERR_PAYMENT_DL_1"|oxmultilangassign:$sPaymentListItem}]
[{assign var="sError_domPaymentListItemTitle_1" value="PAYP_PAYPALPLUS_ERR_PAYMENT_DT_1"|oxmultilangassign}]
[{assign var="sError_domPaymentLabelFormat_1"   value="PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_1"|oxmultilangassign}]
[{assign var="sError_domPaymentLabelChild_1"    value="PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_CHILD_1"|oxmultilangassign}]
[{assign var="sError_domPaymentDescription_1"   value="PAYP_PAYPALPLUS_ERR_PAYMENT_DESC_1"|oxmultilangassign}]
[{assign var="sError_domPaymentIdPrefix_1"      value="PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_1"|oxmultilangassign}]
[{assign var="sError_domPaymentIdPrefix_2"      value="PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_2"|oxmultilangassign}]
[{assign var="sError_domPaymentIdPrefix_3"      value="PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_3"|oxmultilangassign}]
[{assign var="sSettingsTplLocation"      value="PAYP_PAYPALPLUS_SETTINGS_TPL_LOCATION"|oxmultilangassign}]
[{oxscript include=$oView->getPayPalPlusLibraryUrl() priority=1}]
[{oxscript include=$oViewConf->getPayPalPlusSrcUrl('js/payppaypalpluswall.js')|cat:'?108' priority=15}]
[{oxscript add="
jQuery(document).ready(function () {
    jQuery('#`$sWallPlaceholderId`').wall({
        domWallPlaceholderId: '`$sWallPlaceholderId`',
        domExternalButtonId: '`$sExtenalButtonId`',
        domLoadingIndicatorId: '`$sLoadingIndicatorId`',
        domNextStepLink: '`$sNextStepLink`',
        domNextStepLinkParent: '`$sNextStepLinkParent`',
        domPaymentRadioButton: '`$sPaymentRadioButton`',
        domPaymentListItem: '`$sPaymentListItem`',
        domPaymentListItemTitle: '`$sPaymentListItemTitle`',
        domPaymentLabelFormat: '`$sPaymentLabelFormat`',
        domPaymentLabelChild: '`$sPaymentLabelChild`',
        domPaymentDescription: '`$sPaymentDescription`',
        domPaymentIdPrefix: '`$sPaymentIdPrefix`',
        pppApprovalUrl: '`$sApprovalUrl`',
        pppApiMode: '`$sApiMode`',
        pppLanguage: '`$sLanguage`',
        pppCountry: '`$sCountry`',
        errMessage: '`$sErrorMessage`',
        errDefaultMessage: '`$sGeneralErrorCode`',
        varToken: '`$sAjaxResponseToken`',
        varPaymentMethodId: '`$sPayPalPlusMethodId`',
        varExternalMethods: `$sExternalMethods`,
        varRedirectUrl: '`$sExternalMethodRedirectUrl`',
        templateValidationRequired: '`$blTemplateValidationNeeded`',
        isMobile: '`$blMobile`',
        isFlow: '`$blFlow`',
        validationMessages:{
            'PAYP_PAYPALPLUS_ERR_WALL_PLACEHOLDER_1' : '`$sError_domWallPlaceholderId_1`',
            'PAYP_PAYPALPLUS_ERR_EXT_BUTTON_1':'`$sError_domExternalButtonId_1`',
            'PAYP_PAYPALPLUS_ERR_LOADING_1':'`$sError_domLoadingIndicatorId_1`',
            'PAYP_PAYPALPLUS_ERR_NEXT_LINK_1':'`$sError_domNextStepLink_1`',
            'PAYP_PAYPALPLUS_ERR_NEXT_LINK_PARENT_1':'`$sError_domNextStepLinkParent_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_RADIO_BTN_1':'`$sError_domPaymentRadioButton_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_DL_1':'`$sError_domPaymentListItem_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_DT_1':'`$sError_domPaymentListItemTitle_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_1':'`$sError_domPaymentLabelFormat_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_CHILD_1':'`$sError_domPaymentLabelChild_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_DESC_1':'`$sError_domPaymentDescription_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_1':'`$sError_domPaymentIdPrefix_1`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_2':'`$sError_domPaymentIdPrefix_2`',
            'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_3':'`$sError_domPaymentIdPrefix_3`',
            'PAYP_PAYPALPLUS_SETTINGS_TPL_LOCATION':'`$sSettingsTplLocation`'
        }
    });
});"}]
[{oxstyle include=$oViewConf->getPayPalPlusSrcUrl('css/payppaypalpluswall.css')|cat:'?109'}]
<div id="[{$sLoadingIndicatorId}]"></div>
<div id="[{$sWallPlaceholderId}]">
    <noscript>
        <div class="status error">[{$sErrorMessage}]</div>
    </noscript>
</div>