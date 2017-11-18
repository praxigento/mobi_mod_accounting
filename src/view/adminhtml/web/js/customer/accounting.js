define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'Magento_Ui/js/modal/modal',
    'mage/template',
    'text!Praxigento_Accounting/templates/modal/accounting.html'
], function ($, Component, uiRegistry, Modal, mageTemplate, innerHtml) {

    /**
     * Collect working data into the local context.
     */
    /* save globals FROM_KEY */
    var baseUrl = BASE_URL;
    var originUrl = origin; // global var from hell (I don't know from where)
    var formKey = FORM_KEY;
    /* get customer data from uiRegistry */
    var customerDs = uiRegistry.get('customer_form.customer_form_data_source');
    var customer = customerDs.data.customer;
    var customerId = customer.entity_id;

    /**
     * Front-back communication functions
     */
    /* define function to send AJAX request to server */
    var fnGetCustomerData = function () {
        debugger;
        /* switch on loader */
        $('body').trigger('processStart');
        // var customerId = customerId;

        // var url = baseUrl + '/rest/all/V1/prxgt/acc/asset/transfer/init';
        var url = baseUrl + 'customer_accounting/init/';
        // var url = baseUrl + 'edit/accounting/init/';
        var data = {
            customerId: customerId
        };
        /* define function to process response from server */
        var fnSuccess = function (data, status, xhr) {
            debugger;
            if (data.error) {
                alert('Error: ' + data.message);
            } else {
                alert('Done');
            }
            /* switch off loader */
            $('body').trigger('processStop');
        }
        var opts = {
            data: data,
            // contentType: 'application/json',
            type: 'post',
            success: fnSuccess
        };
        debugger;
        $.ajax(url, opts);
    }


    /* functions for accounting slider */
    var fnGetCustomerDataWrap = function () {
        fnGetCustomerData();
    }

    var options = {
        type: 'slide',
        responsive: true,
        innerScroll: true,
        clickableOverlay: true,
        title: $.mage.__('Accounting'),
        buttons: [{
            text: $.mage.__('Process'),
            class: '',
            click: function () {
                this.closeModal();
            }
        }, {
            text: $.mage.__('Get Customer Data'),
            class: '',
            click: fnGetCustomerDataWrap
        }]
    };

    /* function to load data and display slider */
    var fnOpenModal = function () {

        /* see ./view/base/web/templates/modal/accounting.html */
        var modalTmpl = mageTemplate(
            innerHtml,
            {
                formAction: 'changeBalance',
                assetCode: 'assetCode',
                balance: 'balalnce',
                customerName: 'customerName',
                customerEmail: 'customerEmail',
                customerRef: 'customerRef',
                accountId: 'accountId'
            }
        );
        /* set parsed HTML as content for modal placeholder create modal slider */
        $('#modal_panel_placeholder').html(modalTmpl);
        var popup = Modal(options, $('#modal_panel_placeholder'));

        popup.openModal();
        // $('body').trigger('processStart');
    }

    /* bind modal opening to 'Accounting' button on the form */
    /* (see \Praxigento\Accounting\Block\Customer\Adminhtml\Edit\AccountingButton) */
    $("#customer-edit-prxgt-accounting").on("click", fnOpenModal);

    /* this is required return to prevent Magento parsing errors */
    var result = Component.extend({});
    return result;
});