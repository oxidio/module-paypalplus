[{$smarty.block.parent}]
<tr>
    <td class="edittext">
        [{oxmultilang ident="PAYP_PAYPALPLUS_COUNTRY_LOCALE_CODE"}]
    </td>
    <td class="edittext">
        <input type="text" class="editinput" size="40" maxlength="5" name="editval[payppaypalplus_localecode]" value="[{$edit.payppaypalplus_localecode}]" [{ $readonly }]>
        [{ oxinputhelp ident="HELP_PAYP_PAYPALPLUS_COUNTRY_LOCALE_CODE" }]
    </td>
</tr>
