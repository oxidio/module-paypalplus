[{$smarty.block.parent}]
[{assign var="oPayPalPlusPayment" value=$order->getOrderPayment()}]
[{if $oPayPalPlusPayment}]
    [{assign var="oPaymentInstructions" value=$oPayPalPlusPayment->getPaymentInstructions()}]
    [{if $oPaymentInstructions}]
    [{assign var="sDueDate" value=$oPaymentInstructions->getDueDate()|replace:" 00:00:00":""}]
    [{assign var="sTerm" value="PAYP_PAYPALPLUS_PUI_SUCCESS_TERM"|oxmultilangassign:$sDueDate}]
    <table>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">
                [{$sTerm}]
            </td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
            <td>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER"}]:</td>
            <td>[{$oPaymentInstructions->getAccountHolder()}]</td>
        </tr>
        <tr>
            <td>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME"}]:</td>
            <td>[{$oPaymentInstructions->getBankName()}]</td>
        </tr>
        <tr>
            <td>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT"}]:</td>
            <td>[{$oPaymentInstructions->getTotal()}] [{$oPaymentInstructions->getCurrency()}]</td>
        </tr>
        <tr>
            <td>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER"}]:</td>
            <td>[{$oPaymentInstructions->getReferenceNumber()}]</td>
        </tr>
        <tr>
            <td>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN"}]:</td>
            <td>[{$oPaymentInstructions->getIban()}]</td>
        </tr>
        <tr>
            <td>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_BIC"}]:</td>
            <td>[{$oPaymentInstructions->getBic()}]</td>
        </tr>
    </table>
    [{/if}]
[{/if}]
