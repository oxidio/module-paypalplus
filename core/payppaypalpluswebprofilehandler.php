<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @category      module
 * @package       paypalplus
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2016
 */

/**
 * Class paypPayPalPlusWebProfileHandler.
 */
class paypPayPalPlusWebProfileHandler extends paypPayPalPlusSuperCfg
{
    /**
     * @var null|Paypal\Api\FlowConfig
     */
    protected $_oFlowConfig = null;

    /**
     * @var null|Paypal\Api\Presentation
     */
    protected $_oPresentation = null;

    /**
     * @var null|Paypal\Api\InputFields
     */
    protected $_oInputFields = null;

    /**
     * @var null|Paypal\Api\WebProfile
     */
    protected $_oWebProfile = null;

    /**
     * Attempts to create Profile ID
     *
     * @param \PayPal\Rest\ApiContext $oApiContext
     * @param array $aParams
     *
     * @throws \PayPal\Exception\PayPalConnectionException
     *
     * @return \PayPal\Api\CreateProfileResponse
     */
    public function create(PayPal\Rest\ApiContext $oApiContext, $aParams)
    {
        /** Set Params we have and common objects */
        if (!empty($aParams['ExpLogo'])) $this->_getPresentation()->setLogoImage($aParams['ExpLogo']);
        if (!empty($aParams['ExpBrand'])) $this->_getPresentation()->setBrandName($aParams['ExpBrand']);
        if (!empty($aParams['ExpLocale'])) $this->_getPresentation()->setLocaleCode($aParams['ExpLocale']);

        $this->_getWebProfile()->setName($aParams['ExpName']);
        $this->_getWebProfile()->setFlowConfig($this->_getFlowConfig());
        $this->_getWebProfile()->setPresentation($this->_getPresentation());
        $this->_getWebProfile()->setInputFields($this->_getInputFields());

        return $this->_getWebProfile()->create($oApiContext);
    }

    /**
     * Attempts to update Profile data
     *
     * @param \PayPal\Rest\ApiContext $oApiContext
     * @param array $aParams
     *
     * @throws \PayPal\Exception\PayPalConnectionException
     *
     * @return bool
     */
    public function update(PayPal\Rest\ApiContext $oApiContext, $aParams)
    {
        /** Set Params we have and common objects */
        if (!empty($aParams['ExpLogo'])) $this->_getPresentation()->setLogoImage($aParams['ExpLogo']);
        if (!empty($aParams['ExpBrand'])) $this->_getPresentation()->setBrandName($aParams['ExpBrand']);
        if (!empty($aParams['ExpLocale'])) $this->_getPresentation()->setLocaleCode($aParams['ExpLocale']);

        $this->_getWebProfile()->setId($aParams['ExpProfileId']);
        $this->_getWebProfile()->setName($aParams['ExpName']);
        $this->_getWebProfile()->setFlowConfig($this->_getFlowConfig());
        $this->_getWebProfile()->setPresentation($this->_getPresentation());
        $this->_getWebProfile()->setInputFields($this->_getInputFields());

        return $this->_getWebProfile()->update($oApiContext);
    }

    /**
     * Attempts to get Profile data
     *
     * @codeCoverageIgnore Static Call from SDK
     *
     * @param \PayPal\Rest\ApiContext $oApiContext
     * @param string $sProfileId
     *
     * @throws \PayPal\Exception\PayPalConnectionException
     *
     * @return \PayPal\Api\WebProfile
     */
    public function get(PayPal\Rest\ApiContext $oApiContext, $sProfileId)
    {
        $oWebProfile = $this->_getWebProfile();
        return $oWebProfile::get($sProfileId, $oApiContext);
    }

    /**
     * Get's WebProfile object
     *
     * @return Paypal\Api\WebProfile
     */
    protected function _getWebProfile()
    {
        if ($this->_oWebProfile == null) {
            $this->_oWebProfile = $this->getSdk()->newWebProfile();
        }

        return $this->_oWebProfile;
    }

    /**
     * Get's FlowConfig object
     *
     * @return Paypal\Api\FlowConfig
     */
    protected function _getFlowConfig()
    {
        if ($this->_oFlowConfig == null) {

            $this->_oFlowConfig = $this->getSdk()->newFlowConfig();

            /** Preset some static info do not have in settings */
            $this->_oFlowConfig
                ->setLandingPageType("Billing")
                ->setBankTxnPendingUrl("http://www.yeowza.com/");
        }

        return $this->_oFlowConfig;
    }

    /**
     * Get's Presentation object
     *
     * @return Paypal\Api\Presentation
     */
    protected function _getPresentation()
    {
        if ($this->_oPresentation == null) {
            $this->_oPresentation = $this->getSdk()->newPresentation();
        }

        return $this->_oPresentation;
    }

    /**
     * Get's InputFields object
     *
     * @return Paypal\Api\InputFields
     */
    protected function _getInputFields()
    {
        if ($this->_oInputFields == null) {
            $this->_oInputFields = $this->getSdk()->newInputFields();

            /** Preset some static info we do not have in settings */
            $this->_oInputFields
                ->setAllowNote("false")
                ->setNoShipping(0)
                ->setAddressOverride(1);
        }

        return $this->_oInputFields;
    }

}