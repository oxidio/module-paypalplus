[{$smarty.block.parent}]
[{if $payment and $payment->getId() and $payment->getId() eq $oViewConf->getPayPalPlusMethodId()}]
    [{assign var="sExtendedMethodName" value=$oViewConf->getPayPalPlusMethodLabel()}]
    [{oxscript add="jQuery(document).ready(function () {
        var form = jQuery('div#orderPayment > form');
        jQuery('div#orderPayment').html('`$sExtendedMethodName`');
        jQuery('div#orderPayment').prepend(form);
    });"}]
[{/if}]
