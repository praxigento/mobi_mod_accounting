define([
    'jquery',
    'ko',
    'uiComponent',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'text!Praxigento_Accounting/templates/customer/accounting/slider.html'
], function ($, ko, Component, uiRegistry, Modal, mageTemplate, innerHtml) {

    /**
     * Collect working data into the local context (scope).
     */
    /* component constants */
    var TYPE_DIRECT = 'direct';
    var TYPE_BETWEEN = 'between';
    /* pin globals into UI component scope */
    var baseUrl = BASE_URL;
    /* get customer data from uiRegistry */
    var customerDs = uiRegistry.get('customer_form.customer_form_data_source');
    var customer = customerDs.data.customer;
    var customerId = customer.entity_id;
    /* define local working data */
    var baseAdminUrl = baseUrl.replace('/customer/', '/');
    /* see \Praxigento\Downline\Controller\Adminhtml\Customer\Search */
    var urlCustomerSearch = baseAdminUrl + 'prxgt_dwnl/customer/search/';
    /* View Model for slider */
    var viewModel = {
        assets: undefined,
        customer: undefined,
        selectedCounterparty: undefined,
        amount: ko.observable(0),
        counterparty: ko.observable(),
        selectedAsset: ko.observable(),
        transferType: ko.observable(TYPE_DIRECT)
    };


    /**
     * Front-back communication functions
     */
    /* get initial data to fill in slider (customer info, assets balances, etc.) */
    var fnAjaxGetInitData = function () {
        /* switch on ajax loader */
        $('body').trigger('processStart');
        var pinCustomerId = customerId; // pin external scope data

        /* process response from server: create modal slider and populate with data */
        var fnSuccess = function (data) {
            /**
             * Definitions.
             */
            var options = {
                type: 'slide',
                responsive: true,
                innerScroll: true,
                clickableOverlay: true,
                title: $.mage.__('Accounting'),
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }, {
                    text: $.mage.__('Process'),
                    class: '',
                    click: fnAjaxProcessData
                }]
            };

            /* populate template with initial data */
            var fnModalOpen = function () {
                /* set parsed HTML as content for modal placeholder create modal slider */
                $('#modal_panel_placeholder').html(innerHtml);
                var popup = Modal(options, $('#modal_panel_placeholder'));

                /* open modal slider, populate knockout view-model and bind it to template */
                popup.openModal();
                viewModel.amount = ko.observable(0);
                viewModel.assets = data.assets;
                viewModel.counterparty = ko.observable();
                viewModel.customer = data.customer;
                viewModel.transferType = ko.observable(TYPE_DIRECT);
                var elm = document.getElementById('modal_panel_placeholder');
                ko.cleanNode(elm);
                ko.applyBindings(viewModel, elm);
            }

            /**
             * Processing.
             */
            /* switch off loader */
            $('body').trigger('processStop');
            /* open slider */
            fnModalOpen();

            /* add JQuery auto complete widget to the slider */
            $('#prxgtCustomerSearch').autocomplete({
                source: fnAjaxCustomerSearch,
                select: fnAutocompleteSelected,
                minLength: 2,
                /* disable auto-complete helper text */
                messages: {
                    noResults: '',
                    results: function () {
                    }
                }
            });

        }
        /* compose request and perform it */
        var url = baseUrl + 'customer_accounting/init/';
        /* see \Praxigento\Accounting\Controller\Adminhtml\Customer\Accounting\Init::VAR_CUSTOMER_ID*/
        var data = {
            customerId: pinCustomerId
        };
        var opts = {
            data: data,
            type: 'post',
            success: fnSuccess
        };
        $.ajax(url, opts);
    }

    /**
     * Search customers on server and prepare data for UI.
     *
     * @param request
     * @param response
     */
    var fnAjaxCustomerSearch = function (request, response) {
        $.ajax({
            url: urlCustomerSearch,
            data: {
                /* see: \Praxigento\Downline\Controller\Adminhtml\Customer\Search::VAR_SEARCH_KEY*/
                search_key: request.term
            },
            success: function (data) {
                /* convert API data into JQuery widget data */
                var found = [];
                for (var i in data.items) {
                    var one = data.items[i];
                    var nameFirst = one.name_first;
                    var nameLast = one.name_last;
                    var email = one.email;
                    var id = one.id;
                    var mlmId = one.mlm_id;
                    var label = nameFirst + ' ' + nameLast + ' <' + email + '> / ' + mlmId;
                    var foundOne = {
                        label: label,
                        value: label,
                        data: one
                    };
                    found.push(foundOne);
                }
                response(found);
            }
        });
    }

    var fnAjaxProcessData = function () {
        var asset = viewModel.selectedAsset();
        var assetId = asset.asset_id;
        var amount = viewModel.amount();
        var customerId = viewModel.customer.id;
        var counterpartyId = viewModel.selectedCounterparty;
        var type = viewModel.transferType();
        if (type == TYPE_BETWEEN) {

        }
        debugger;
    }

    var fnAutocompleteSelected = function (event, ui) {
        viewModel.selectedCounterparty = ui.item.data.id;
        debugger;
    }

    /* bind modal opening to 'Accounting' button on the form */
    /* (see \Praxigento\Accounting\Block\Customer\Adminhtml\Edit\AccountingButton) */
    $('#customer-edit-prxgt-accounting').on('click', fnAjaxGetInitData);

    /* this is required return to prevent Magento parsing errors */
    var result = Component.extend({});
    return result;
});