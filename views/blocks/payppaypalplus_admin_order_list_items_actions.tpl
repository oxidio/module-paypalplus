[{if $listitem->oxorder__oxstorno->value == 1}]
    [{assign var="listclass" value=listitem3}]
[{else}]
    [{if $listitem->blacklist == 1}]
        [{assign var="listclass" value=listitem3}]
    [{else}]
        [{assign var="listclass" value=listitem$blWhite}]
    [{/if}]
[{/if}]
[{if $listitem->getId() == $oxid}]
    [{assign var="listclass" value=listitem4}]
[{/if}]
<td valign="top" height="15" class="[{$listclass}]" nowrap="nowrap">
    <div class="listitemfloating">
        <a href="Javascript:top.oxid.admin.editThis('[{$listitem->oxorder__oxid->value}]');" class="[{$listclass}]">
            [{$listitem->getPaymentName()}]
            [{if $listitem->hasPaymentInstructions()}][{oxmultilang ident='PAYP_PAYPALPLUS_PUI'}][{/if}]
            [{if $listitem->hasInvoice()}]&#10004;[{/if}]
            &nbsp;
        </a>
    </div>
</td>
[{$smarty.block.parent}]