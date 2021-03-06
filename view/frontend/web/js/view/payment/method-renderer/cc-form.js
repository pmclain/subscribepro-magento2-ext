/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'Magento_Payment/js/view/payment/cc-form',
        'Swarming_SubscribePro/js/model/payment/cc-form',
        'Swarming_SubscribePro/js/model/payment/config',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer'
    ],
    function ($, Component, CcForm, config, quote, customer) {
        'use strict';

        return Component.extend(CcForm).extend({
            defaults: {
                template: 'Swarming_SubscribePro/payment/cc-form',
                canPlaceOrder: false
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'canPlaceOrder'
                    ]);
                return this;
            },

            initialize: function () {
                this._super();

                quote.billingAddress.subscribe(function (address) {
                    this.canPlaceOrder(address !== null && this.isValidHostedFields && this.isValidExpDate);
                }, this);
            },

            updateSaveActionAllowed: function () {
                this.canPlaceOrder(quote.billingAddress() != null && this.isValidHostedFields && this.isValidExpDate)
            },

            isActive: function () {
                return this.getCode() == this.isChecked();
            },

            getData: function () {
                return {
                    'method': this.getCode(),
                    'additional_data': {
                        'is_active_payment_token_enabler': customer.isLoggedIn(),
                        'payment_method_token': this.paymentMethodToken()
                    }
                };
            },

            getPaymentData: function () {
                return {
                    'first_name': quote.billingAddress().firstname,
                    'last_name': quote.billingAddress().lastname,
                    'phone_number': quote.billingAddress().telephone,
                    'address1': quote.billingAddress().street[0],
                    'address2': quote.billingAddress().street[1] || '',
                    'city': quote.billingAddress().city,
                    'state': quote.billingAddress().regionCode,
                    'zip': quote.billingAddress().postcode,
                    'country': quote.billingAddress().countryId,
                    'year': this.creditCardExpYear(),
                    'month': this.creditCardExpMonth()
                };
            },

            submitPayment: function () {
                this.placeOrder();
            }
        });
    }
);
