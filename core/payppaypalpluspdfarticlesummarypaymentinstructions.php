<?php
/**
 * Class paypPayPalPlusPdfArticleSummaryPaymentInstructions
 *
 * Handle the data retrieval and formatting of the payment instructions of an order.
 * Set the instance of the PdfArticleSummary in order to add the text lines there.
 */
class paypPayPalPlusPdfArticleSummaryPaymentInstructions
{

    /**
     * An instance of PdfArticleSummary resp. InvoicepdfArticleSummary.
     * This depends on the shop version.
     *
     * @var PdfArticleSummary|InvoicepdfArticleSummary
     */
    protected $_oPdfArticleSummary;

    /**
     * An instance of paypPayPalPlusPuiData.
     *
     * @var paypPayPalPlusPuiData
     */
    protected $_oPaymentInstructions;

    /**
     * Language Id as selected in create Invoice box in the admin backend.
     *
     * @var integer
     */
    protected $_iLang;

    /**
     * An instance of the order the invoice should be created for.
     *
     * @var oxOrder
     */
    protected $_oOrder;

    /**
     * A string representing the shop owner.
     *
     * @var string
     */
    protected $_sShopOwner;

    /**
     * Property getter.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    public function getLang()
    {
        return $this->_iLang;
    }

    /**
     * Property setter
     *
     * @codeCoverageIgnore
     *
     * @param mixed $iLang language id as used throughout the OXID shop
     */
    public function setLang($iLang)
    {
        $this->_iLang = $iLang;
    }

    /**
     * @return oxOrder
     */
    public function getOrder()
    {
        return $this->_oOrder;
    }

    /**
     * @param oxOrder $oOrder
     */
    public function setOrder(oxOrder $oOrder)
    {
        $this->_oOrder = $oOrder;
    }

    /**
     * Set the PdfArticleSummary or InvoicepdfArticleSummary instance.
     * This the text property of this instance will be updated by this class.
     *
     * Property setter
     *
     * @codeCoverageIgnore
     *
     * @param PdfArticleSummary|InvoicepdfArticleSummary $oPdfArticleSummary
     */
    public function setPdfArticleSummary(&$oPdfArticleSummary)
    {
        $this->_oPdfArticleSummary = $oPdfArticleSummary;
    }

    /**
     * Property setter
     *
     * @codeCoverageIgnore
     *
     * @param paypPayPalPlusPuiData $oPaymentInstructions
     */
    public function setPaymentInstructions(paypPayPalPlusPuiData $oPaymentInstructions)
    {
        $this->_oPaymentInstructions = $oPaymentInstructions;
    }

    /**
     * Add the payment instructions to the article summary.
     *
     * This function is not testable.
     *
     * @codeCoverageIgnore
     *
     * @param $iStartPos
     * @param $iLang
     */
    public function addPaymentInstructions(&$iStartPos, $iLang)
    {
        $this->setLang($iLang);
        $sDueDate = $this->_getFormattedDate($this->_oPaymentInstructions->getDueDate());
        $sAccountHolder = $this->_oPaymentInstructions->getAccountHolder();
        $sBankName = $this->_oPaymentInstructions->getBankName();
        $sAmount = $this->_getFormattedTotal($this->getOrder()->oxorder__oxtotalordersum->value) . ' ' . $this->_oPaymentInstructions->getCurrency();
        $sReferenceNumber = $this->_oPaymentInstructions->getReferenceNumber();
        $sIban = $this->_oPaymentInstructions->getIban();
        $sBic = $this->_oPaymentInstructions->getBic();

        $sLegalNotice = $this->_translateString('PAYP_PAYPALPLUS_PUI_SUCCESS_LEGAL_NOTICE');
        $sLegalNotice = sprintf($sLegalNotice, $this->_getShopOwner());

        $sTerm = $this->_translateString('PAYP_PAYPALPLUS_PUI_SUCCESS_TERM');
        $sTerm = vsprintf($sTerm, array($sAmount, $sDueDate));

        $aTextLines = $this->_getPaymentInstructionsTextLines($sLegalNotice, $sTerm, $sBankName, $sAccountHolder, $sIban, $sBic, $sAmount, $sReferenceNumber);

        foreach ($aTextLines as $text) {
            $this->_oPdfArticleSummary->font($this->_oPdfArticleSummary->getFont(), '', 10);
            $this->_oPdfArticleSummary->text(15, $iStartPos + 4, $text);
            $iStartPos += 4;
        }
    }

    /**
     * Return a translation for a given string.
     *
     * @param $sString
     *
     * @return string
     */
    protected function _translateString($sString)
    {
        /** Use AdminMode false. We fetch the translation from the frontend translation files */
        $blAdminMode = false;
        $iLang = $this->getLang();

        /** Parameters for html_entity_decode()  */
        $sFlags = ENT_QUOTES;
        $sEncoding = 'UTF-8';

        $sTranslatedString = oxRegistry::getLang()->translateString($sString, $iLang, $blAdminMode);
        $sTranslatedString = html_entity_decode($sTranslatedString, $sFlags, $sEncoding);

        return $sTranslatedString;
    }

    /**
     * Format a single line as displayed in the payment instructions.
     *
     * @param $sLabel
     * @param $sValue
     *
     * @return string
     */
    protected function _getTextLine($sLabel, $sValue)
    {
        return $this->_translateString($sLabel) . ': ' . $sValue;
    }

    /**
     * Format the due date as displayed in the payment instructions.
     * The formatting options are retrieved from the _lang.php files of the module.
     *
     * @param $sDate string A date string in the format 'Y-m-d H:i:s'
     *
     * @return string
     */
    protected function _getFormattedDate($sDate)
    {
        $sDateFormatString = $this->_translateString('PAYP_PAYPALPLUS_PUI_SUCCESS_DATE_FORMAT');
        $oDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $sDate);
        if ($oDateTime) {
            $sDate = $oDateTime->format($sDateFormatString);
        }

        return $sDate;
    }

    /**
     * Format the order total as displayed in the payment instructions.
     * The formatting options are retrieved from the _lang.php files of the module.
     *
     * @param $fTotal
     *
     * @return mixed
     */
    protected function _getFormattedTotal($fTotal)
    {

        $sDecimals = $this->_translateString('PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMALS');
        $sDecimalSeparator = $this->_translateString('PAYP_PAYPALPLUS_PUI_CURRENCY_DECIMAL_SEPARATOR');
        $sThousandsSeparator = $this->_translateString('PAYP_PAYPALPLUS_PUI_CURRENCY_THOUSANDS_SEPARATOR');

        $sTotal = number_format($fTotal, $sDecimals, $sDecimalSeparator, $sThousandsSeparator);;

        return str_replace('.', $sDecimalSeparator, $sTotal);
    }

    /**
     * Do the formatting of the payment instructions text lines as a total.
     *
     * @param $sLegalNotice
     * @param $sTerm
     * @param $sBankName
     * @param $sAccountHolder
     * @param $sIban
     * @param $sBic
     * @param $sAmount
     * @param $sReferenceNumber
     *
     * @return array
     */
    protected function _getPaymentInstructionsTextLines($sLegalNotice, $sTerm, $sBankName, $sAccountHolder, $sIban, $sBic, $sAmount, $sReferenceNumber)
    {
        /** @var integer $iLineWidth Line width in characters, adapt this if you change the font size */
        $iLineWidth = 90;
        /** @var string $sWrapBreak Delimiter string for wordwrap */
        $sWrapBreak = '###';
        /** @var array $aTextLines Initial lines array. It starts with a blank line */
        $aTextLines = array('');

        /** Merge rest of params into text lines */
        $aTextLines = array_merge(
            $aTextLines,
            array(
                $sTerm,
                '',
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_BANK_NAME', $sBankName),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_ACCOUNT_HOLDER', $sAccountHolder),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_IBAN', $sIban),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_BIC', $sBic),
                '',
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_AMOUNT', $sAmount),
                $this->_getTextLine('PAYP_PAYPALPLUS_PUI_SUCCESS_REFERENCE_NUMBER', $sReferenceNumber),
            )
        );

        /** Add 2 blank lines */
        $aTextLines = array_merge(
            $aTextLines,
            array('', '')
        );

        /** Merge legal notice into text lines */
        $aLegalNotice = $this->_explodeLongString($sLegalNotice, $sWrapBreak, $iLineWidth);
        $aTextLines = array_merge($aTextLines, $aLegalNotice);

        /** Add 2 blank lines */
        $aTextLines = array_merge(
            $aTextLines,
            array('', '')
        );

        return $aTextLines;
    }

    /**
     * Return the shop owner string.
     *
     * @return string
     */
    protected function _getShopOwner()
    {
        $sShopOwner = $this->_getShopOwnerFromModuleSettings();
        if (empty($sShopOwner)) {
            $sShopOwner = $this->_getShopOwnerFromShopCoreSettings();
        }

        return $sShopOwner;
    }

    /**
     * Return the shop owner as configured in the module settings.
     *
     * @return string
     */
    protected function _getShopOwnerFromModuleSettings()
    {
        $sShopOwner = '';

        $oModule = oxNew('oxModule');
        if ($oModule->load('payppaypalplus')) {
            $sShopOwner = $oModule->getConfig()->getConfigParam('paypPayPalPlusShopOwnerStr');
        }

        return $sShopOwner;
    }

    /**
     * Return the shop owner as configured in the shop settings.
     *
     * @return string
     */
    protected function _getShopOwnerFromShopCoreSettings()
    {
        $sShopOwner = '';

        $oOrder = $this->getOrder();
        $shopId = $oOrder->getShopId();
        $oShop = oxNew('oxShop');
        if ($oShop->load($shopId)) {
            $sShopOwner = empty($oShop->oxshops__oxcompany) ? trim($oShop->oxshops__oxfname->value . ' ' . $oShop->oxshops__oxlname->value) : $oShop->oxshops__oxcompany->value;
        }

        return $sShopOwner;
    }

    /**
     * Convert a long string into an array of $iLineWidth length strings.
     *
     * @param string  $sLegalNotice Legal notice text string
     * @param string  $sWrapBreak   Break character for wordwrap and explode delimiter string
     * @param integer $iLineWidth   Number of characters in each line
     *
     * @return array
     */
    protected function _explodeLongString($sLegalNotice, $sWrapBreak = '###', $iLineWidth = 90)
    {
        /** Wrap the legal notice text and explode it into an array */
        $aLegalNotice = explode($sWrapBreak, wordwrap($sLegalNotice, $iLineWidth, $sWrapBreak));

        return $aLegalNotice;
    }
}