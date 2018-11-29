[{$smarty.block.parent}]
[{assign var="oPayPalPlusPayment" value=$order->getOrderPayment()}]
[{if $oPayPalPlusPayment}]
    [{assign var="oPaymentInstructions" value=$oPayPalPlusPayment->getPaymentInstructions()}]
    [{if $oPaymentInstructions}]

    [{assign var="sDueDate" value=$oPaymentInstructions->getDueDate()|replace:" 00:00:00":""}]
    [{assign var="sTerm" value="PAYP_PAYPALPLUS_PUI_SUCCESS_TERM"|oxmultilangassign:$sDueDate}]

    [{$sTerm}]
    -----------------------------------------------------------------------------------------------------------------------
    [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER"}]:    [{$oPaymentInstructions->getAccountHolder()}]
    [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME"}]:    [{$oPaymentInstructions->getBankName()}]
    [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT"}]:    [{$oPaymentInstructions->getTotal()}] [{$oPaymentInstructions->getCurrency()}]
    [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER"}]:    [{$oPaymentInstructions->getReferenceNumber()}]
    [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN"}]:    [{$oPaymentInstructions->getIban()}]
    [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_BIC"}]:    [{$oPaymentInstructions->getBic()}]
    [{/if}]
[{/if}]
