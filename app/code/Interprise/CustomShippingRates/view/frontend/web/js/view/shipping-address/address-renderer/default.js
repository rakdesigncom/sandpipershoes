/**

 * Copyright © Magento, Inc. All rights reserved.

 * See COPYING.txt for license details.

 */



define([

    'jquery',

    'ko',

    'uiComponent',

    'Magento_Checkout/js/action/select-shipping-address',

    'Magento_Checkout/js/model/quote',

    'Magento_Checkout/js/model/shipping-address/form-popup-state',

    'Magento_Checkout/js/checkout-data',

    'Magento_Customer/js/customer-data'

], function ($, ko, Component, selectShippingAddressAction, quote, formPopUpState, checkoutData, customerData) {

    'use strict';



    var countryData = customerData.get('directory-data');



    return Component.extend({

        defaults: {

            template: 'Magento_Checkout/shipping-address/address-renderer/default'

        },



        /** @inheritdoc */

        initObservable: function () {

            this._super();

            this.isSelected = ko.computed(function () {

                var isSelected = false,

                shippingAddress = quote.shippingAddress();



                if (shippingAddress) {
                    // setTimeout(() => {
                        isSelected = shippingAddress.getKey() == this.address().getKey(); //eslint-disable-line eqeqeq
                    // }, 2000);
                    

                }

                var newaddid=0; 

                if(isSelected==true){

                    var newaddid = shippingAddress.customerAddressId;  

                    if(newaddid==undefined){

                        newaddid=0;

                    }

                    console.log("newid",newaddid);              

                    var customurl = "https://www.sandpipershoes.com/trade/setaddresssession/index/index";

                    this.ajaxcall(customurl,newaddid).then(res=>{

                        console.log(res);
                        // location.reload();

                    })

                }



                return isSelected;

            }, this);


            
            return this;

        },



        /**

         * @param {String} countryId

         * @return {String}

         */

        getCountryName: function (countryId) {

            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line

        },



        ajaxcall: function(customurl,newaddid){

            return new Promise(function (resolve, reject) {

                $.ajax({

                    url: customurl,

                    type: 'POST',

                    dataType: 'json',

                    data: {

                        newaddid: newaddid,

                    },

                    complete: function(response) {             

                        resolve(true);  

                    },

                    error: function (xhr, status, errorThrown) {

                        resolve(false);

                        console.log('Error happens. Try again.'+errorThrown);

                        console.log('Error happens..'+status);

                    }

                });

            })

        },



        /** Set selected customer shipping address  */

        selectAddress: function () {
            // setTimeout(function(){
                selectShippingAddressAction(this.address());

                checkoutData.setSelectedShippingAddress(this.address().getKey());
            // },4000);
            

        },



        /**

         * Edit address.

         */

        editAddress: function () {

            formPopUpState.isVisible(true);

            this.showPopup();



        },



        /**

         * Show popup.

         */

        showPopup: function () {

            $('[data-open-modal="opc-new-shipping-address"]').trigger('click');

        }

    });

});

