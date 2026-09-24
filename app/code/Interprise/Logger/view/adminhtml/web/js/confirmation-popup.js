define([
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'jquery',
    'ko',
    'mage/translate',
    'mage/template',
    'underscore',
    'Magento_Ui/js/modal/alert',
], function (Component, confirm, $, ko, $t, template, _, alert) {

    'use strict';

    return Component.extend({
        /**
         * Initialize Component
         */
        initialize: function () {
            var self = this,
                content;

            this._super();

            content = $t('Are you sure you want to sync order to Interprise?') ;

            console.log(content)

            /**
             * Confirmation popup
             *
             * @param {String} url
             * @returns {Boolean}
             */
            window.interpriseSyncConfirmationPopup = function (url) {
                confirm({
                    title: 'Sync order to Interprise',
                    content: content,
                    modalClass: 'confirm lac-confirm',
                    actions: {
                        confirm: function () {
                            var formKey = $('input[name="form_key"]').val(),
                                params = {};

                            if (formKey) {
                                params.form_key = formKey;
                            }
                            // jscs:enable requireCamelCaseOrUpperCaseIdentifiers

                            $.ajax({
                                url: url,
                                type: 'POST',
                                dataType: 'json',
                                data: params,
                                showLoader: true,

                                /**
                                 * Open redirect URL in new window, or show messages if they are present
                                 *
                                 * @param {Object} data
                                 */
                                success: function (data) {
                                    var messages = data.messages || [];

                                    if (messages.length) {
                                        messages = messages.map(function (message) {
                                            return _.escape(message);
                                        });
                                        alert({
                                            content: messages.join('<br>')
                                        });
                                    } else {
                                        window.location.reload();
                                    }
                                },

                                /**
                                 * Show XHR response text
                                 *
                                 * @param {Object} jqXHR
                                 */
                                error: function (jqXHR) {
                                    alert({
                                        content: _.escape(jqXHR.responseText)
                                    });
                                }
                            });
                        }
                    },
                    buttons: [{
                        text: $t('Cancel'),
                        class: 'action-secondary action-dismiss',

                        /**
                         * Click handler.
                         */
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }, {
                        text: $t('Sync Order'),
                        class: 'action-primary action-accept',

                        /**
                         * Click handler.
                         */
                        click: function (event) {
                            this.closeModal(event, true);
                        }
                    }]
                });

                return false;
            };
        }
    });
});
