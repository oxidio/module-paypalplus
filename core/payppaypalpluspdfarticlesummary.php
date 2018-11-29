<?php
/**
 * Class paypPayPalPlusPdfArticleSummary
 *
 * Extend PdfArticleSummary to be able to display additional payment instructions after the article summary.
 *
 * Third party integration and not testable in all shop versions
 *
 * @codeCoverageIgnore
 */
if (class_exists('PdfArticleSummary')) {
    class paypPayPalPlusPdfArticleSummary extends PdfArticleSummary
    {

        /**
         * @inheritdoc
         *
         * Add the possibility to add payment instructions to the due date or 'PayUntilInfo', as it is called in the parent
         * function.
         */
        protected function _setPayUntilInfo(&$iStartPos)
        {
            $oPaymentInstructions = $this->_getPaymentInstructions();

            if ($oPaymentInstructions) {
                $iLang = $this->_oData->getSelectedLang();
                $oPdfArticleSummaryPaymentInstructions = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
                $oPdfArticleSummaryPaymentInstructions->setPdfArticleSummary($this);
                $oPdfArticleSummaryPaymentInstructions->setPaymentInstructions($oPaymentInstructions);
                $oPdfArticleSummaryPaymentInstructions->setOrder($this->_getOrder());
                $oPdfArticleSummaryPaymentInstructions->addPaymentInstructions($iStartPos, $iLang);
            } else {
                $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYUPTO') . date('d.m.Y', strtotime('+' . $this->_oData->getPaymentTerm() . ' day', strtotime($this->_oData->oxorder__oxbilldate->value)));
                $this->font($this->getFont(), '', 10);
                $this->text(15, $iStartPos + 4, $text);
                $iStartPos += 4;
            }
        }

        /**
         * Return an instance of the related order.
         *
         * @return oxOrder
         */
        protected function _getOrder()
        {
            $sOrderId = $this->_getOrderId();
            $oOrder = oxNew('oxOrder');
            $oOrder->load($sOrderId);

            return $oOrder;
        }

        /**
         * Get the payment instructions from the order.
         *
         * @return null|paypPayPalPlusPuiData|void
         */
        protected function _getPaymentInstructions()
        {
            $oPaymentInstructions = null;

            $sOrderId = $this->_getOrderId();

            $oOrder = oxNew('oxOrder');
            if ($oOrder->load($sOrderId)) {
                $oPaymentInstructions = $oOrder->getPaymentInstructions();
            }

            return $oPaymentInstructions;
        }

        /**
         * Return the ID or the current order.
         * Needed for testing.
         *
         * @codeCoverageIgnore
         *
         * @return mixed
         */
        protected function _getOrderId()
        {
            $sOrderId = $this->_oData->getId();

            return $sOrderId;
        }
    }
}