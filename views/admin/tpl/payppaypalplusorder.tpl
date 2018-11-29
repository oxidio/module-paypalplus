[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]
<link rel="stylesheet" href="[{$oViewConf->getPayPalPlusSrcUrl('css/payppaypalplusbackend.css')}]"/>
<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]"/>
    <input type="hidden" name="oxidCopy" value="[{$oxid}]"/>
    <input type="hidden" name="cl" value="Admin_paypPayPalPlusOrderTab"/>
    <input type="hidden" name="language" value="[{$actlang}]"/>
</form>
[{if $oView->isPayPalPlusOrder()}]
    [{assign var="oOrderPayment" value=$oView->getOrderPayment()}]
    [{assign var="dRefundedAmount" value=$oOrderPayment->getTotalAmountRefunded()}]
    [{assign var="oPaymentInstructions" value=$oOrderPayment->getPaymentInstructions()}]
    <table width="98%" cellspacing="0" cellpadding="0" border="0">
        <tbody>
        <tr>
            <td class="edittext" valign="top" width="50%">
                <b>[{oxmultilang ident="PAYP_PAYPALPLUS_PAYMENT_OVERVIEW"}]</b>
                <table class="paypPayPalPlusOverviewTable">
                    <tbody>
                    <tr>
                        <td class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_PAYMENT_STATUS"}]:</td>
                        <td class="edittext"><b>[{$oOrderPayment->getStatus()}]</b></td>
                    </tr>
                    <tr>
                        <td class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_ORDER_AMOUNT"}]:</td>
                        <td class="edittext"><b>[{$oView->formatPrice($oOrderPayment->getTotal())}]</b></td>
                    </tr>
                    [{if $dRefundedAmount}]
                        <tr>
                            <td class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_REFUNDED_AMOUNT"}]:</td>
                            <td class="edittext"><b>[{$oView->formatPrice($dRefundedAmount)}]</b></td>
                        </tr>
                        [{/if}]
                    <tr>
                        <td class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_PAYMENT_ID"}]:</td>
                        <td class="edittext"><b>[{$oOrderPayment->getPaymentId()}]</b></td>
                    </tr>
                    <tr>
                        <td class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_PAYMENT_METHOD"}]:</td>
                        <td class="edittext">
                            <b>[{if $oPaymentInstructions}][{oxmultilang ident="PAYP_PAYPALPLUS_PUI"}][{else}]PayPal[{/if}]</b></td>
                    </tr>
                    </tbody>
                </table>
                [{if $oPaymentInstructions}]
                <div style="height: 25px">&nbsp;</div>
                <b>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_PAYMENT_INSTRUCTIONS"}]</b>
                <table>
                    <tr>
                        <td>
                            [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_TERM"}]:
                        </td>
                        <td>
                            [{$oPaymentInstructions->getDueDate()|replace:" 00:00:00":""}]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_ACCOUNT_HOLDER"}]:
                        </td>
                        <td>
                            [{$oPaymentInstructions->getAccountHolder()}]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_BANK_NAME"}]:
                        </td>
                        <td>
                            [{$oPaymentInstructions->getBankName()}]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_REFERENCE_NUMBER"}]:
                        </td>
                        <td>
                            [{$oPaymentInstructions->getReferenceNumber()}]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_IBAN"}]:
                        </td>
                        <td>
                           [{$oPaymentInstructions->getIban()}]
                        </td>
                    </tr>
                    <tr>
                        <td>
                            [{oxmultilang ident="PAYP_PAYPALPLUS_PUI_BIC"}]:
                        </td>
                        <td>
                            [{$oPaymentInstructions->getBic()}]
                        </td>
                    </tr>
                </table>
                [{/if}]

            </td>
            <td class="edittext" valign="top" align="left" width="50%">
                [{if $oOrderPayment->isRefundable()}]
                [{assign var="oOrderPaymentRefunds" value=$oOrderPayment->getRefundsList()}]
                [{assign var="dRemainingRefundAmount" value=$oView->getRemainingRefundAmount()}]
                <b>[{oxmultilang ident="PAYP_PAYPALPLUS_PAYMENT_REFUNDING"}]</b>
                <table class="paypPayPalPlusOverviewTable" cellpadding="0" border="0">
                    <tbody>
                    [{if $dRemainingRefundAmount}]
                        <tr>
                            <td width="5%">&nbsp;</td>
                            <td width="40%" class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_AVAILABLE_REFUNDS"}]
                            </td>
                            <td width="35%" class="edittext"><b>[{$oView->getRemainingRefundsCount()}]</b></td>
                            <td width="20%">&nbsp;</td>
                        </tr>
                        [{/if}]
                    <tr>
                        <td>&nbsp;</td>
                        <td class="edittext">[{oxmultilang ident="PAYP_PAYPALPLUS_AVAILABLE_REFUND_AMOUNT"}]</td>
                        <td class="edittext"><b>[{$oView->formatPrice($dRemainingRefundAmount)}]</b></td>
                        <td>&nbsp;</td>
                    </tr>
                    [{if $oOrderPaymentRefunds and $oOrderPaymentRefunds->count()}]
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <th class="listheader first">&nbsp;</th>
                            <th class="listheader">[{oxmultilang ident="PAYP_PAYPALPLUS_DATE"}]</th>
                            <th class="listheader" height="15">[{oxmultilang ident="PAYP_PAYPALPLUS_AMOUNT"}]</th>
                            <th class="listheader">[{oxmultilang ident="PAYP_PAYPALPLUS_STATUS"}]</th>
                        </tr>
                        [{foreach name='refunds_list' from=$oOrderPaymentRefunds item=oOrderPaymentRefund}]
                        <tr>
                            <td valign="top" class="listitem edittext">#[{$smarty.foreach.refunds_list.iteration}]</td>
                            <td valign="top" class="listitem edittext">[{$oOrderPaymentRefund->getDateCreated()}]</td>
                            <td valign="top" class="listitem edittext" height="15">
                                [{$oView->formatPrice($oOrderPaymentRefund->getTotal())}]
                            </td>
                            <td valign="top" class="listitem edittext">[{$oOrderPaymentRefund->getStatus()}]</td>
                        </tr>
                        [{/foreach}]
                        [{/if}]
                    [{if $oView->isRefundPossible()}]
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        [{assign var="sRefundError" value=$oView->getRefundErrorMessage()}]
                        [{if $sRefundError}]
                        <tr>
                            <td colspan="4">
                                <div class="errorbox">[{$sRefundError}]</div>
                            </td>
                        </tr>
                        [{/if}]
                        <tr>
                            <td>&nbsp;</td>
                            <td class="edittext"><b>[{oxmultilang ident="PAYP_PAYPALPLUS_NEW_REFUND"}]</b></td>
                            <td class="edittext" colspan="2">
                                <form id="refund" name="myedit" action="[{$oViewConf->getSelfLink()}]"
                                      method="post">
                                    [{$oViewConf->getHiddenSid()}]
                                    <input type="hidden" name="cl" value="Admin_paypPayPalPlusOrderTab"/>
                                    <input type="hidden" name="fnc" value="actionRefund"/>
                                    <input type="hidden" name="oxid" value="[{$oxid}]"/>
                                    <input type="hidden" name="saleId" value="[{$oOrderPayment->getSaleId()}]"/>
                                    <input type="text" class="editinput" size="7" maxlength="8"
                                           name="refundAmount" value=""/>&nbsp;[{$oView->getPaymentCurrencyCode()}]
                                    <input type="submit" class="edittext paypPayPalPlusRefundButton" name="refund"
                                           value="[{oxmultilang ident="PAYP_PAYPALPLUS_REFUND"}]"/>
                                </form>
                            </td>
                        </tr>
                        [{/if}]
                    </tbody>
                </table>
                [{/if}]
            </td>
        </tr>
        </tbody>
    </table>
    [{else}]
    <div class="messagebox">[{oxmultilang ident="PAYP_PAYPALPLUS_ONLY_FOR_PAYPAL_PLUS_PAYMENT"}]</div>
    [{/if}]
[{include file="bottomnaviitem.tpl"}]
[{include file="bottomitem.tpl"}]