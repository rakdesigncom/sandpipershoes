define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'mage/url',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/modal'
], function ($, ko, Component, customer, quote, storege, url, getTotalsAction, mageAlert, modal) {
    'use strict';
    var isLoading = ko.observable(true);
    var isApplied = ko.observable(false);
    var configValues = window.checkoutConfig;
    var customerNotes = configValues.customerNotes;
    var productsName = configValues.productsName;
    var deferred = $.Deferred();
    var reasons = configValues.vatReasons;
    var linkUrl = url.build('exempt/index');
    var quoteId = quote.getQuoteId();
    var quoteData = window.checkoutConfig.quoteData;
    var exemptname = ko.observable(quoteData.vat_exempt_customer);
    var exemptreason = ko.observable(quoteData.vat_exempt_reason);
    var self = this;
    var acceptTerms = configValues.acceptTerms;
    var chkAccpt = ((quoteData.vat_exempt_reason !="")?"checked":"");
    var disApply = ((quoteData.vat_exempt_reason !="")?false:true);
    var disCancel = ((quoteData.vat_exempt_reason !="")?true:false);

    var isLogin = window.checkoutConfig.isLogin;

    return Component.extend({
        defaults: {
            template: 'Meetanshi_VatExempt/checkout/vat-exempt-declain'
        },
        initialize: function () {
            this._super();

            /*if (isLogin != 0) {
                if (typeof customer.customerData.id === 'undefined' || customer.customerData.id === 'undefined') {
                    setTimeout(function () {
                        jQuery('#login-msg').parent().css('display', 'block');
                    }, 1000);
                }
            }*/

            /*if( quoteData.vat_exempt_customer != "" )
            {   $('#exemptname').val(quoteData.vat_exempt_customer);   }

            if( quoteData.vat_exempt_reason != "" )
            {
                $("#exemptreason option:selected").prop("selected",false);   $("#exemptreason option[value='"+quoteData.vat_exempt_reason+"']").prop("selected",true);
            }*/

        },
        exemptname: ko.observable(quoteData.vat_exempt_customer),
        isLoading: ko.observable(false),
        getCustomerNotes: ko.observable(customerNotes),
        getProductsName: ko.observable(productsName),
        getReasons: ko.observableArray(reasons),
        accept: ko.observable(acceptTerms),
        selectedReasons: ko.observable(quoteData.vat_exempt_reason),
        successMessageHold: ko.observable(false),
        successMessage: ko.observable(),
        chkAccept: ko.observable(chkAccpt),
        disApl: ko.observable(disApply),
        disCncl: ko.observable(disCancel),
        apply: function () {
            if (isLogin != 0 && (typeof customer.customerData.id === 'undefined' || customer.customerData.id === 'undefined'))
            {
                // jQuery('#login-msg').parent().css('display', 'block');

                mageAlert({
                    title: 'Warning!',
                    content: ' <div class="field warning message">\n' +
                        '<span id="login-msg" data-bind="test: loginMessage">Login must be required for VAT Exemption</span>\n' +
                        '</div>',
                    actions: {
                        always: function(){}
                    }
                });

            }
            else
            {
                // jQuery('#login-msg').parent().css('display', 'none');
                var form = '#vat-exempt-declain',
                    formDataArray = $(form).serializeArray(),
                    vatFormData = {};
                vatFormData['apply'] = true;
                vatFormData['quoteId'] = quoteId;
                if (this.validate(form)) {
                    formDataArray.forEach(function (entry) {
                        vatFormData[entry.name] = entry.value;
                    })
                    isLoading(true);
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: linkUrl,
                        data: JSON.stringify(vatFormData),
                        success: function (data) {
                            var deferred = $.Deferred();
                            getTotalsAction([], deferred);
                            isLoading(true);
                            $('.success.message').css('display', 'block');
                            document.getElementById('applied').innerText = data.message;
                            $('#action-apply').attr('disabled', 'disabled');
                            // $('#action-cancel').removeAttrs('disabled');
                            $('#action-cancel').removeAttr('disabled');
                            document.getElementById('processed').value = 1;
                        }
                    });
                }
            }
        },
        isApplied: function () {
            return false;
        },
        openDialog: function (elementId, modalId, element) {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                buttons: [{
                    text: $.mage.__('Continue'),
                    class: 'button action primary',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            var popup = modal(options, $('#' + modalId));
            $(element).find('#' + elementId).click(function(e) {
                e.preventDefault();
                $('#' + modalId).modal("openModal");
            });
        },

        cancel: function () {
            var form = '#vat-exempt-declain',
                formDataArray = $(form).serializeArray(),
                vatFormData = {};
            vatFormData['apply'] = false;
            vatFormData['quoteId'] = quoteId;
            if (this.validate(form)) {
                formDataArray.forEach(function (entry) {
                    vatFormData[entry.name] = entry.value;
                })
                isLoading(true);
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: linkUrl,
                    data: JSON.stringify(vatFormData),
                    success: function (data) {
                        var deferred = $.Deferred();
                        isLoading(true);
                        getTotalsAction([], deferred);
                        document.getElementById('exemptname').value = "";
                        document.getElementById('exemptreason').value = "";
                        document.getElementById('accept').checked = false;
                        $('.success.message').css('display', 'block');
                        document.getElementById('applied').innerText = data.message;
                        $('#action-cancel').attr('disabled', 'disabled');
                        // $('#action-apply').removeAttrs('disabled');
                        $('#action-apply').removeAttr('disabled');
                        document.getElementById('processed').value = 0;
                    }
                });
            }
        },
        validate: function (form) {
            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
