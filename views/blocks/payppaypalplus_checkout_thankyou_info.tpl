[{$smarty.block.parent}]
[{assign var="oPaymentInstructions" value=$oView->getPaymentInstructions()}]

[{oxstyle include=$oViewConf->getPayPalPlusSrcUrl('css/payppaypalpluswall.css')|cat:'?106'}]
[{oxscript include=$oViewConf->getPayPalPlusSrcUrl('js/payppaypalplusthankyou.js')|cat:'?106'}]

[{if $oPaymentInstructions }]

    [{assign var="sTotalPrice" value=$oView->getTotalPrice()}]
    [{assign var="sCurrency" value=$oPaymentInstructions->getCurrency()}]
    [{assign var="sDueDate" value=$oView->getDueDate()}]
    [{assign var="aTermParams" value=$oView->getTermParams() }]
    [{assign var="sTerm" value="PAYP_PAYPALPLUS_PUI_SUCCESS_TERM"|oxmultilangassign:$aTermParams}]
    [{assign var="sDescription" value="PAYP_PAYPALPLUS_PUI_SUCCESS_DESCRIPTION"|oxmultilangassign}]

    <div class="payppaypalpui-paymentinstructions">
        <div class="header">[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_PAYMENT_INSTRUCTIONS"}]</div>
        <div class="container">
            <a href="javascript:;" class="link  print-payment-instructions">[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_PRINT_INSTRUCTIONS"}]</a>
            <div class="top">
                    <span class="amount">
                    [{$sTotalPrice}] [{$sCurrency}]
                    </span>
                <div class="calendar">
                    <img src="[{$oViewConf->getPayPalPlusSrcUrl('img/PP_PLUS_PUI_ArrowGraphic.png')|cat:'?106'}]" alt="calendar">
                </div>
                <span class="paypal-logo">
                    <img src="https://www.paypalobjects.com/webstatic/de_DE/i/de-pp-logo-100px.png" border="0" alt="PayPal Logo"/>
                </span>
            </div>
            <div class="middle">
                <div class="term">[{$sTerm}]</div>
            </div>
            <div class="bottom">
                <div class="table">
                    <ul>
                        <li><label>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER"}]:</label>[{$oPaymentInstructions->getAccountHolder()}]</li>
                        <li><label>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME"}]:</label>[{$oPaymentInstructions->getBankName()}]</li>
                        <li><label>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT"}]:</label>[{$sTotalPrice}] [{$sCurrency}]</li>
                        <li><label>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER"}]:</label>[{$oPaymentInstructions->getReferenceNumber()}]</li>
                        <li><label>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN"}]:</label>[{$oPaymentInstructions->getIban()}]</li>
                        <li><label>[{oxmultilang ident="PAYP_PAYPALPLUS_PUI_SUCCESS_BIC"}]:</label>[{$oPaymentInstructions->getBic()}]</li>
                    </ul>
                </div>
                <div class="description">
                    [{$sDescription}]
                </div>
            </div>
        </div>
    </div>
[{/if}]