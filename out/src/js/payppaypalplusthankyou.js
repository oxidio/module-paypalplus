$(document).ready(function(){
    $('.payppaypalpui-paymentinstructions').delegate('a.print-payment-instructions', 'click', function() {
        $('head').append(
            '<style type="text/css" media="print">' +
            '#header, #breadcrumb, .checkoutSteps, #incVatMessage, #footer, #alsoBoughtThankyou, h1.pageHead { display: none; }' +
            '</style>');
        window.print();
    });
});
