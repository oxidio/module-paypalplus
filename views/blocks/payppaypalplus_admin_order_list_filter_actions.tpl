<td valign="top" class="listfilter first" height="20">
    <div class="r1">
        <div class="b1">
            <select name="payppaypalpluspayment" onChange="document.search.submit();">
                <option value="-1" style="color: #000000;">[{oxmultilang ident="PAYP_PAYPALPLUS_LIST_STATUS_ALL"}]</option>
                [{foreach key=id item=aPayment  from=$aPayments}]
                    <option value="[{$id}]" [{if $payppaypalpluspayment == $id}]SELECTED[{/if}] >[{$aPayment.name}]</option>
                [{/foreach}]
            </select>
        </div>
    </div>
</td>
[{$smarty.block.parent}]