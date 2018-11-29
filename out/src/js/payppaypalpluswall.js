/**
 * jQuery "wall" widget for PayPal Plus wall integration on OXID eShop payment page.
 */
jQuery.widget(
    'payppaypalplus.wall',
    {

        /**
         * Widget options with default values (where possible).
         */
        options: {
            domWallPlaceholderId: '',
            domExternalButtonId: '',
            domLoadingIndicatorId: '',
            domNextStepLink: '',
            domNextStepLinkParent: '',
            domPaymentRadioButton: '',
            domPaymentListItem: '',
            domPaymentListItemTitle: '',
            domPaymentLabelFormat: '',
            domPaymentLabelChild: '',
            domPaymentDescription: '',
            domPaymentIdPrefix: '',
            pppApprovalUrl: '',
            pppApiMode: '',
            pppLanguage: '',
            pppCountry: '',
            errMessage: '',
            errDefaultMessage: '',
            varToken: '',
            varPaymentMethodId: '',
            varExternalMethods: [],
            varRedirectUrl: '',
            templateValidationRequired: '',
            isMobile: '',
            isFlow: '',
            validationMessages: {}, //{ 'translationKey' : 'translatedErrorMessage', ... }
            jsPaymentPlaceHolder: '%s',
            skipPayment: ['oxempty']//Usually there is no "free" cart. On the other hand there is no point to validate payment on "free" cart.
        },

        /**
         * Get validation error key.
         *
         * @private
         */
        _elementValidator: {
            lastCheckedSelector: '',
            options: {},
            paymentMethods: [],
            validate_domWallPlaceholderId: function () {
                var selector = '#' + this.options.domWallPlaceholderId;
                if (!( jQuery(selector).length)) {
                    this.setLastCheckedSelector(selector);
                    throw 'PAYP_PAYPALPLUS_ERR_WALL_PLACEHOLDER_1';
                }
            },
            validate_domExternalButtonId: function () {
                if (!(jQuery('#' + this.options.domExternalButtonId).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_EXT_BUTTON_1';
                }
            },
            validate_domLoadingIndicatorId: function () {
                if (!(jQuery('#' + this.options.domLoadingIndicatorId).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_LOADING_1';
                }
            },
            /**
             * Validates Top Next link and its parent.
             * @returns {*}
             */
            validate_domNextStepLink: function () {
                if (this.options.domNextStepLink) {
                    if (!(jQuery(this.options.domNextStepLink).length)) {
                        throw 'PAYP_PAYPALPLUS_ERR_NEXT_LINK_1';
                    }

                    this._validate_domNextStepLinkParent();
                }
            },
            validate_domPaymentListItem: function () {
                if (!(jQuery(this.options.domPaymentListItem).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_DL_1';
                }
            },
            /**
             * Execute validations  for each configured payment.
             */
            validate_payments: function () {
                var index;
                var paymentMethods = this.getPaymentMethodSet();
                var length = paymentMethods.length;
                var paymentId;
                for (index = 0; index < length; ++index) {
                    paymentId = paymentMethods[index];
                    this.validate_domPaymentListItemTitle(paymentId);
                    this.validate_domPaymentIdPrefix(paymentId);
                    this.validate_domPaymentRadioButton(paymentId);
                    this.validate_domPaymentLabelFormat(paymentId);
                    //this.validate_domPaymentDescription(paymentId);
                }
            },
            validate_domPaymentIdPrefix: function (paymentId) {
                var selector = '#' + this.getPaymentIdHtml(paymentId);
                this.setLastCheckedSelector(selector);
                if (!(jQuery(selector).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_1';
                }
                if (!(jQuery(selector).is(this.options.domPaymentRadioButton))) {
                    this.setLastCheckedSelector([selector, this.options.domPaymentRadioButton]);
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_3';
                }
                if (!this.options.isMobile) {
                    this._validate_PaymentParents(selector);
                }
            },
            /**
             * Validate payment selection input field for one payment
             * @param paymentId
             */
            validate_domPaymentRadioButton: function (paymentId) {
                var selector = '#' + this.getPaymentIdHtml(paymentId);
                this.setLastCheckedSelector(selector);
                if (!(jQuery(selector).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_RADIO_BTN_1';
                }
            },
            /**
             * Validate payment label exists also check its children.
             * @returns {*}
             */
            validate_domPaymentLabelFormat: function (paymentId) {
                var selector = this.options.domPaymentLabelFormat.replace('%s', paymentId);
                this.setLastCheckedSelector(selector);
                if (!(jQuery(selector).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_1';
                }
                if (!this.options.isMobile) {
                    this._validate_domPaymentLabelChild(selector);
                }
            },
            validate_domPaymentListItemTitle: function () {
                var selector = this.options.domPaymentListItem + ' ' + this.options.domPaymentListItemTitle;
                this.setLastCheckedSelector(selector);
                if (!(jQuery(selector).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_DT_1';
                }
            },
            validate_domPaymentDescription: function (paymentId) {
                var selector = this.options.domPaymentDescription;
                var element = jQuery('#payment_' + paymentId).parents('dl').find(selector);
                this.setLastCheckedSelector(paymentId);
                if (paymentId != 'payppaypalplus' && this.options.domPaymentDescription && !(jQuery(element).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_DESC_1';
                }
            },
            /**
             * Validates top nest link parent.
             *
             * @returns {string}
             */
            _validate_domNextStepLinkParent: function () {
                var selector = this.options.domNextStepLinkParent + ' ' + this.options.domNextStepLink;
                if (!(jQuery(selector).length)) {
                    this.setLastCheckedSelector(this.options.domNextStepLinkParent);
                    throw 'PAYP_PAYPALPLUS_ERR_NEXT_LINK_PARENT_1';
                }
            },
            /**
             * Validation is performed under parent context
             *
             * @param parentSelector
             * @returns {string}
             */
            _validate_domPaymentLabelChild: function (parentSelector) {
                var selector = parentSelector + ' ' + this.options.domPaymentLabelChild;
                this.setLastCheckedSelector(selector);
                if (!(jQuery(selector).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_LABEL_CHILD_1';
                }
            },
            /**
             * Check payment select input is under right parents.
             *
             * @param paymentSelector
             * @private
             */
            _validate_PaymentParents: function (paymentSelector) {
                var selector = this.options.domPaymentListItem +
                    ' ' +
                    this.options.domPaymentListItemTitle +
                    ' ' +
                    paymentSelector;
                this.setLastCheckedSelector(selector);
                if (!(jQuery(selector).length)) {
                    throw 'PAYP_PAYPALPLUS_ERR_PAYMENT_PREFIX_2';
                }
            },
            /**
             * Helper method to construct html payment id from oxPaymentId
             * @param oxPaymentId
             * @returns {string}
             */
            getPaymentIdHtml: function (oxPaymentId) {
                return this.options.domPaymentIdPrefix + oxPaymentId;
            },

            /**
             * Get last validated selectors or other params. value can be string or array
             * @returns {string}
             */
            getLastCheckedSelector: function () {
                return this.lastCheckedSelector;
            },
            /**
             * Payment setter. Used to track payment validation
             * @param paymentSelector  string|array
             */
            setLastCheckedSelector: function (paymentSelector) {
                this.lastCheckedSelector = paymentSelector;
            },
            /**
             * Array of payment method ids.
             * @returns {*}
             */
            getPaymentMethodSet: function () {
                return this.paymentMethods;
            },
            /**
             * Push payemnt methods one by one. Method chain supported.
             * @param sMethodId
             * @returns {*}
             */
            pushPaymentMethod: function (sMethodId) {
                this.paymentMethods.push(sMethodId);
                return this;
            },
            /**
             * Option setter.
             * @param options  object
             * @returns {*}
             */
            setOptions: function (options) {
                this.options = options;
                return this;
            }
        },

        /**
         * Error box class name.
         *
         * @private
         */
        _errorClass: 'paypPayPalPlusWallError',

        /**
         * Maximum length of payment method name field.
         *
         * @private
         */
        _methodNameMaxLength: 25,

        /**
         * Maximum length of payment method description field.
         *
         * @private
         */
        _methodDescriptionMaxLength: 1000,

        /**
         * PayPal Plus Wall instance.
         *
         * @private
         */
        _ppp: null,

        /**
         * Stores flag if PayPalPlus payment is selected.
         *
         * @private
         */
        _pppMethodActive: true,

        /**
         * PayPal Plus method flag setter. Update on payment method is selected (PayPalPlus or other).
         * Default value is TRUE, Method chain supported.
         *
         * @param blFlag
         * @returns {*}
         */
        setPPPMethodActive: function (blFlag) {
            this._pppMethodActive = typeof blFlag !== 'undefined' ? blFlag : true;
            return this;
        },

        /**
         * Show or hide loading indicator.
         *
         * @param show
         */
        toggleLoadingIndicator: function (show) {
            var loadingIndicator = jQuery('#' + this.options.domLoadingIndicatorId);
            if (show) {
                loadingIndicator.show();
            } else {
                loadingIndicator.hide();
            }
        },

        /**
         * Constructor.
         * Bind payment methods changing events and initialize PayPal Plus method (PPP wall).
         *
         * @private
         */
        _create: function () {
            // bind general event handlers
            this._bindPaymentMethodChangedHandler();
            this._bindWallIsLoadedHandler();


             // Always load the wall, so the radio buttons of the payment methods included in the wall get hidden
            this._loadWall();

            if (this._isPayPalPlusTheOnlyPaymentMethod()) {
                // Preselect PayPalPlus
                this._payPalPlusMethodPreselection();
                // Show Wall
                this._showPayPalPlusWall();
                //Show Payment Description if one exists
                this._showPayPalPlusDescription();
                // disable controls
                this._disableControls();
                // bind event handler to "next step button"
                this._bindNextButtonClickHandler();
            } else {
                if (this._isPayPalPlusSelected()) {
                    // Show Wall
                    this._showPayPalPlusWall();
                    //Show Payment Description if one exists
                    this._showPayPalPlusDescription();
                    // disable controls
                    this._disableControls();
                    // bind event handler to "next step button"
                    this._bindNextButtonClickHandler();
                } else {
                    // Hide the wall
                    this._removePayPalPlusWall();
                    //Hide Payment Description if one exists
                    this._hidePayPalPlusDescription();
                    // enable controls
                    this._enableControls();
                    // unbind event handler from "next step button"
                    this._unbindNextButtonClickHandler();
                }
            }
        },

        /**
         * External button click event defined.
         * Collects payment form data to send it for validation.
         * On success triggers PPP checkout, on failure shows error.
         *
         * @private
         */
        _bindNextButtonClickHandler: function () {
            var _this = this;
            jQuery('body').delegate('#' + this.options.domExternalButtonId, 'click', function (event) {
                var button = this;
                _this._nextButtonClickHandler(event, button);
            });
        },

        /**
         * Unbind/undelegate and enable "next" button.
         *
         * @private
         */
        _unbindNextButtonClickHandler: function () {
            jQuery('body').undelegate('#' + this.options.domExternalButtonId, 'click');
        },

        /**
         * Bind an event handler to the "wall is loaded" event. At the moment the only way to tell if the wall is loaded
         * is to listen to the document ready event of the iframe, which happens far sooner than its visible content is
         * shown.
         *
         * @private
         */
        _bindWallIsLoadedHandler: function () {
            var _this = this;
            jQuery('#' + this.options.domWallPlaceholderId + ' iframe').ready(function () {
                _this._wallIsLoadedHandler();
            });
        },

        /**
         * Bind a handler to the "payment method changed" event
         *
         * @private
         */
        _bindPaymentMethodChangedHandler: function () {
            var _this = this;
            jQuery(this.options.domPaymentRadioButton).change(function () {
                _this._paymentMethodChangedHandler();
            });

            jQuery("#sPaymentSelected").change(function () {
                _this._paymentMethodChangedHandler();
            });
        },

        /**
         * Handler for the click on the "next" button
         * @private
         */
        _nextButtonClickHandler: function (event, button) {
            event.preventDefault();

            jQuery('div.' + this._errorClass).remove();

            this.toggleLoadingIndicator(true);
            this._validatePaymentAndCheckout($(button).parents('form'));
        },

        /**
         * Payment method changed event.
         * If PayPal Plus method was selected:
         * - load the wall,
         * - show it
         * - disable controls
         * - bind handler to click on "next" button
         *
         * If a different method was selected:
         * - hide the wall
         * - enable controls
         * - unbind handler from click on "next" button
         *
         * @private
         */
        _paymentMethodChangedHandler: function () {
            if (this._isPayPalPlusSelected()) {
                // Show Wall
                this._showPayPalPlusWall();
                //Show Payment Description if one exists
                this._showPayPalPlusDescription();
                // Load the wall
                this._loadWall();
                // disable controls
                this._disableControls();
                // bind event handler to "next step button"
                this._bindNextButtonClickHandler();
            } else {
                // Hide the wall
                this._removePayPalPlusWall();
                //Hide payment description if one exists
                this._hidePayPalPlusDescription();
                // enable controls
                this._enableControls();
                // unbind event handler from "next step button"
                this._unbindNextButtonClickHandler();
            }
        },

        /**
         * Remove the loading indicator when the PayPal Plus Wall is loaded
         *
         * @private
         */
        _wallIsLoadedHandler: function () {
            this.toggleLoadingIndicator(false);
        },

        /**
         * If PayPal Plus method is available, initialize the wall.
         *
         * @private
         */
        _loadWall: function () {
            if (jQuery('#' + this.options.domPaymentIdPrefix + this.options.varPaymentMethodId).length > 0) {
                if (jQuery('#' + this.options.domWallPlaceholderId).is(":visible")) {
                    var loadingIndicator = jQuery('#' + this.options.domLoadingIndicatorId);
                    loadingIndicator.appendTo('body');
                }
                this._render();
            } else {
                this._showError(this.options.errMessage, false);
            }
        },

        /**
         * Activate loading indicator an call PPP wall initialization.
         * Stop loading indicator on success or show error on failure.
         *
         * @private
         */
        _render: function () {
            var _this = this;

            /** Remove previously outputed errors not to see them after payment was reselected */
            jQuery('div.' + this._errorClass).remove();

            /** Show the loading indicator */
            this.toggleLoadingIndicator(true);

            /** Validate DOM options (integration settings) */
            if (!this._validateDomElements()) {
                this._showError(this.options.errMessage, false);
                return;
            }

            /**  Move PayPal Plus placeholder to visible area */
            this._makePlaceholderVisible();

            /** Initialize the wall */
            try {
                this._ppp = PAYPAL.apps.PPP({
                    approvalUrl: this.options.pppApprovalUrl,
                    placeholder: this.options.domWallPlaceholderId,
                    mode: this.options.pppApiMode,
                    preselection: 'none',
                    buttonLocation: 'outside',
                    disableContinue: this.options.domExternalButtonId,
                    enableContinue: this.options.domExternalButtonId,
                    language: this.options.pppLanguage,
                    country: this.options.pppCountry,
                    showPuiOnSandbox: true,
                    onLoad: function () {
                        _this.toggleLoadingIndicator(false); //TODO: PPP onLoad does not work so far
                    },
                    thirdPartyPaymentMethods: _this._collectExternalPaymentMethods(),
                    //TODO: Once available also register payment method selection event and similar stuff.
                    onThirdPartyPaymentMethodSelected: function () {
                        _this.setPPPMethodActive(false);
                    },
                    onThirdPartyPaymentMethodDeselected: function () {
                        _this.setPPPMethodActive(true);
                    }
                });

                // Chrome/IE fix
                jQuery('#' + this.options.domWallPlaceholderId + ' iframe').css('width', '100%');
            } catch (error) {
                this._showError(error, false);
                this._showError(this.options.errMessage, false);
            }

            /** hide the loading indicator */
            this.toggleLoadingIndicator(false);
        },

        /**
         * Send payment validation and payment update AJAX request.
         * On success proceed with PPP checkout.
         * Also sets a small delay to make sure loading indicator triggers on all browsers.
         *
         * @param form {object}
         * @private
         */
        _validatePaymentAndCheckout: function (form) {
            var _this = this;
            var payPalCheckout = false;
            var sError = _this.options.errMessage;
            var requestData = form.serializeArray();
            requestData.push({name: 'ajax', value: '1'});
            requestData.push({name: 'pppMethodActive', value: _this._isPPPMethodActive() ? 1 : 0});

            jQuery.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: requestData,
                async: true,
                cache: false,
                success: function (mData, sTextStatus) {
                    if (sTextStatus == 'success' && mData == _this.options.varToken) {
                        payPalCheckout = true;
                    } else if (mData && mData != _this.options.errDefaultMessage) {
                        sError = mData;
                    }

                    _this._removeError();//remove errors after each async request is done.
                    if (payPalCheckout) {
                        try {
                            PAYPAL.apps.PPP.doCheckout();
                        } catch (err) {
                            _this._showError(sError, true);
                        }
                    } else {
                        _this._showError(sError, true);
                    }
                },
                error: function (response) {
                    _this._showError(_this.options.errMessage, true);
                }
            });

            setTimeout(
                function () {
                    _this._showError(_this.options.errMessage, false);
                },
                30000
            );
        },

        /**
         * PayPal Plus radio button auto selection.
         * If PayPal Plus is the only (visible) payment method, the radio select button is not shown.
         * Nevertheless another (invisible) payment method could be selected and this would lead to errors.
         * This function selects the PayPal Plus button, if needed.
         *
         * @private
         */
        _payPalPlusMethodPreselection: function () {
            if (this.options.isMobile) {
                var wrapperSelector = this.options.domPaymentLabelFormat.replace('%s', this.options.varPaymentMethodId);
                jQuery(wrapperSelector).trigger('click');
            } else {
                var visiblePaymentIdRadioButtons = jQuery('input[name=paymentid]:visible');
                var paypalplus_radio = jQuery('#' + this.options.domPaymentIdPrefix + this.options.varPaymentMethodId);

                if (
                    !visiblePaymentIdRadioButtons.length
                    && paypalplus_radio.length > 0
                    && !paypalplus_radio.is(':checked')
                ) {
                    paypalplus_radio.trigger('click');
                    paypalplus_radio.trigger('change'); // For older jQuery versions
                }
            }
        },

        /**
         * Validate all important dom elements, if Template Validation is checked in module settings
         *
         * @private
         */
        _validateDomElements: function () {
            if (this.options.templateValidationRequired == 0 ) {
                return true;
            }

            var errorMessage;
            var isValid = true;
            this._initValidator();
            try {
                this._elementValidator.validate_domWallPlaceholderId();
                this._elementValidator.validate_domExternalButtonId();
                this._elementValidator.validate_domLoadingIndicatorId();
                this._elementValidator.validate_domNextStepLink();
                //'domNextStepLinkParent',//validated if next link exists
                this._elementValidator.validate_domPaymentListItem();
                this._elementValidator.validate_payments();
                //'domPaymentIdPrefix', //validate inside payment
                //'domPaymentRadioButton', //validate inside payment
                //'domPaymentListItemTitle', //validates inside payments
                //'domPaymentDescription', //validates inside payments
                //'domPaymentLabelFormat', //validates inside payments
                //'domPaymentLabelChild', //validates inside domPaymentLabelFormat
            } catch (errorKey) {
                errorMessage = this._getErrorMessageFromCode(errorKey);
                if (typeof errorKey != "undefined" && errorKey && typeof errorMessage != "undefined" && errorMessage) {
                    errorMessage = this._decorateErrorMessage(errorMessage);
                    this._showError(errorMessage, false);
                    isValid = false;
                }
            }
            return isValid;
        },

        /**
         * Set required params(options and payment methods) to validator.
         * @private
         */
        _initValidator: function () {
            var paymentMethod;
            var length = this.options.varExternalMethods.length;
            var index;
            this._elementValidator.setOptions(this.options);

            this._elementValidator.pushPaymentMethod(this.options.varPaymentMethodId);
            for (index = 0; index < length; ++index) {
                paymentMethod = this.options.varExternalMethods[index];
                if (jQuery.inArray(paymentMethod, this.options.skipPayment) >= 0) {
                    continue;
                }
                this._elementValidator.pushPaymentMethod(this.options.varExternalMethods[index]);
            }
        },

        /**
         * If the wall placeholder is inside hidden list item content, move it to visible list item title.
         *
         * @private
         */
        _makePlaceholderVisible: function () {
            var placeholder = jQuery('#' + this.options.domWallPlaceholderId);
            var listItem = placeholder.parents(this.options.domPaymentListItem);
            var listItemTitle = listItem.children(this.options.domPaymentListItemTitle);

            if (listItem.length > 0 && listItemTitle.length > 0) {
                listItemTitle.append(placeholder.parent().children());
            }
        },

        /**
         * Collect payment methods that are configured as external and hide them from radio buttons list.
         * In case there are no more payment methods but PayPal Plus, its radio button is also hidden.
         *
         * @private
         */
        _collectExternalPaymentMethods: function () {
            var configuredMethods = this.options.varExternalMethods;
            var redirectUrl = this.options.varRedirectUrl;
            var methodId;
            var methods = [];
            var i = 0;
            var _this = this;

            jQuery(this.options.domPaymentRadioButton).each(function (index, element) {
                methodId = jQuery(element).val();

                if (jQuery.inArray(methodId, configuredMethods) >= 0 &&
                    jQuery.inArray(methodId, _this.options.skipPayment) == -1) {
                    methods[i++] = {
                        redirectUrl: redirectUrl + methodId,
                        methodName: _this._getPaymentMethodLabel(methodId),
                        description: _this._getPaymentMethodDescription(methodId)
                    };
                    if (!_this.options.isMobile) {
                        jQuery(element).parents(_this.options.domPaymentListItem).hide();
                    } else {
                        var wrapperSelector = _this.options.domPaymentLabelFormat.replace('%s', methodId);
                        jQuery(wrapperSelector).parents(_this.options.domPaymentListItem).hide();
                    }
                }
            });

            if (jQuery(this.options.domPaymentRadioButton + ':visible').length == 1) {
                jQuery(this.options.domPaymentRadioButton + ':visible').hide();
            }

            if (this.options.isMobile && this._isPayPalPlusTheOnlyPaymentMethod()) {
                jQuery(this.options.domPaymentListItem).parent().parent().hide();
            }

            return methods;
        },

        /**
         * Get payment method label by payment method ID.
         * If it is empty, method ID is used. And if the label is too long, it is cropped to fit maximum length.
         *
         * @param methodId {string}
         * @returns {string}
         * @private
         */
        _getPaymentMethodLabel: function (methodId) {
            var selector = this.options.domPaymentLabelFormat.replace('%s', methodId);

            if (!this.options.isMobile && this.options.domPaymentLabelChild.length > 0) {
                selector = selector + ' ' + this.options.domPaymentLabelChild;
            }

            var methodLabelElement = jQuery(selector);
            var methodLabel = methodId;

            if (methodLabelElement.length > 0) {
                methodLabel = this._trimString(methodLabelElement.text());
            }

            methodLabel = this._truncateString(methodLabel, this._methodNameMaxLength);

            return methodLabel
        },

        /**
         * Parse a clean payment method description field value from a raw text.
         *
         * @param methodId {string}
         * @returns {string}
         * @private
         */
        _getPaymentMethodDescription: function (methodId) {
            var labelSelector = this.options.domPaymentLabelFormat.replace('%s', methodId);
            var label = jQuery(labelSelector);
            var description = '';

            if (label.length > 0) {
                if (this.options.isMobile) {
                    var wrapperSelector = this.options.domPaymentDescription.replace('%s', methodId);
                    var wrapper = jQuery(wrapperSelector);
                } else {
                    var wrapper = label.parents(this.options.domPaymentListItem).find(this.options.domPaymentDescription);
                }

                if (wrapper.length > 0) {
                    description = wrapper.text();
                }
            }

            description = this._trimString(description);
            description = this._truncateString(description, this._methodDescriptionMaxLength);

            return description;
        },

        /**
         * Convert error code to error message. If message not found - return empty string.
         *
         * @param code Error code as a string
         * @returns string
         *
         * @private
         */
        _getErrorMessageFromCode: function (code) {
            if (typeof code !== "undefined") {
                var errorMessage = this.options.validationMessages[code];
                if (typeof errorMessage != "undefined" && errorMessage) {
                    return errorMessage;
                }
            }
            return '';
        },

        /**
         * Trim string and remove any new lines, tabs and multiple spaces from inside the string.
         *
         * @param text {string}
         * @returns {string}
         * @private
         */
        _trimString: function (text) {
            text = text.trim();
            text = text.replace(/(\r\n|\n|\r|\t)/gm, '');
            text = text.replace(/\s{2,}/g, ' ');

            return text;
        },

        /**
         * Truncate string to a certain max length.
         *
         * @param text {string}
         * @param maxLength {int}
         * @returns {string}
         * @private
         */
        _truncateString: function (text, maxLength) {
            if (text.length > maxLength) {
                text = text.substring(0, (maxLength - 3)) + '...';
            }

            return text;
        },

        /**
         * Do some post processing. Handle some place holder and info that available during validation only.
         *
         * @param errorMessage
         * @returns {string}
         * @private
         */
        _decorateErrorMessage: function (errorMessage) {
            var index;
            var aParams = this._elementValidator.getLastCheckedSelector();
            if (!jQuery.isArray(aParams)) {
                aParams = [aParams];
            }
            var length = aParams.length;
            for (index = 0; index < length; ++index) {
                errorMessage = errorMessage.replace(this.options.jsPaymentPlaceHolder, aParams[index]);
            }

            errorMessage = errorMessage
                + '<br/>'
                + this._getErrorMessageFromCode('PAYP_PAYPALPLUS_SETTINGS_TPL_LOCATION');
            return errorMessage;
        },

        /**
         * PayPal Plus method flag getter. True PayPal Plus payment method was selected, False - other method was selected.
         * @returns {boolean}
         *
         * @private
         */
        _isPPPMethodActive: function () {
            return this._pppMethodActive;
        },

        /**
         * Return true, if PayPal Plus is the only payment method on the page.
         *
         * @private
         */
        _isPayPalPlusTheOnlyPaymentMethod: function () {
            if (this.options.isMobile) {
                return (jQuery(this.options.domPaymentListItem + ":not([style$='display: none;'])").length < 2);
            } else {
                var visiblePaymentIdRadioButtons = $('input[name=paymentid]:visible');
                var paypalplus_radio = jQuery('#' + this.options.domPaymentIdPrefix + this.options.varPaymentMethodId);


                return !visiblePaymentIdRadioButtons.length
                    && paypalplus_radio.length > 0
                    && !paypalplus_radio.is(':checked');
            }
        },

        /**
         * Return true, if PayPal Plus is the selected Payment method
         * @private
         */
        _isPayPalPlusSelected: function () {
            return jQuery(this.options.domPaymentRadioButton + ':checked').val() == this.options.varPaymentMethodId;
        },

        /**
         * Enable navigation elements and buttons, which will lead to the next step in checkout.
         *
         * @private
         */
        _enableControls: function () {
            jQuery('#' + this.options.domExternalButtonId).removeAttr('disabled');
            this._enableNavigation();
        },

        /**
         * Enable navigation elements and buttons, which will lead to the next step in checkout.
         * This is because a payment method may not have been chosen within the PayPal Plus wall.
         *
         * @private
         */
        _disableControls: function () {
            jQuery('#' + this.options.domExternalButtonId).attr('disabled', 'disabled');
            this._disableNavigation();
        },

        /**
         * Replace next link with just a text from the link.
         * This is a checkout navigation link being disabled.
         *
         * @private
         */
        _enableNavigation: function () {
            var linkToNextStep = jQuery(this.options.domNextStepLink);
            var parentOfLinkToNextStep = linkToNextStep.parent(this.options.domNextStepLinkParent);

            if (linkToNextStep.length > 0 && parentOfLinkToNextStep.length > 0) {
                linkToNextStep
                    .removeClass('paypPayPalPlus')
                    .removeClass('inactive')
                ;
            }
        },

        /**
         * Replace next link with just a text from the link.
         * This is a checkout navigation link being disabled.
         *
         * @private
         */
        _disableNavigation: function () {
            var linkToNextStep = jQuery(this.options.domNextStepLink);
            var parentOfLinkToNextStep = linkToNextStep.parent(this.options.domNextStepLinkParent);

            if (linkToNextStep.length > 0 && parentOfLinkToNextStep.length > 0) {
                linkToNextStep
                    .addClass('paypPayPalPlus')
                    .addClass('inactive')
                ;
            }
        },

        /**
         * Stop loading indicator and add an error message box.
         * Optionally reload the PPP wall.
         *
         * @param message {string}
         * @param refreshWall {boolean}
         *
         * @private
         */
        _showError: function (message, refreshWall) {
            jQuery('#' + this.options.domWallPlaceholderId).before(
                '<div class="status error ' + this._errorClass + '">' + message + '</div>'
            );

            if (refreshWall) {
                this._loadWall();
            }
            this.toggleLoadingIndicator(false);
        },

        /**
         * Remove old error block. Just after submitting request to server.
         *
         * @private
         */
        _removeError: function () {
            jQuery('#' + this.options.domWallPlaceholderId).prevAll('.status.error').first().remove();
        },

        /**
         * Show the wall.
         *
         * @private
         */
        _showPayPalPlusWall: function () {
            jQuery('#' + this.options.domWallPlaceholderId).show();
        },

        /**
         * Show the wall.
         *
         * @private
         */
        _showPayPalPlusDescription: function () {
            var selector = jQuery(this.options.domPaymentListItem + ' ' + this.options.domPaymentDescription);
            if (selector.length > 0) {
                jQuery(selector).show();
            }
        },

        /**
         * Hide the wall.
         *
         * @private
         */
        _removePayPalPlusWall: function () {
            jQuery('#' + this.options.domWallPlaceholderId).hide();
            jQuery('#' + this.options.domWallPlaceholderId + ' iframe').empty().remove();
        },

        /**
         * Hide payment description
         *
         * @private
         */
        _hidePayPalPlusDescription: function () {
            var selector = jQuery(this.options.domPaymentListItem + ' ' + this.options.domPaymentDescription);
            if (selector.length > 0) {
                jQuery(selector).hide();
            }
        }
    }
);
