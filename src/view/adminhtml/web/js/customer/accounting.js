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
    /* get initial data to fill in slider (customer info, assets balances, etc.) */
    var fnAjaxGetInitData = function () {
        /* switch on ajax loader */
        $('body').trigger('processStart');
        var pinCustomerId = customerId; // pin external scope data

        /* define function to process response from server */
        var fnSuccess = function (data, status, xhr) {
            if (data.error) {
                alert('Error: ' + data.message);
            } else {
                alert(JSON.stringify(data));
            }
            /* switch off loader */
            $('body').trigger('processStop');
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


    /* functions for accounting slider; TODO: remove me */
    var fnGetCustomerDataWrap = function () {
        fnAjaxGetInitData();
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

    /**
     * Slider definition.
     */
    /* function to load data and display slider */
    var fnModalOpen = function () {

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

        /* open modal slider and start ajax request to get init data */
        popup.openModal();
        fnAjaxGetInitData();
    }

    /* bind modal opening to 'Accounting' button on the form */
    /* (see \Praxigento\Accounting\Block\Customer\Adminhtml\Edit\AccountingButton) */
    $("#customer-edit-prxgt-accounting").on("click", fnModalOpen);

    /* this is required return to prevent Magento parsing errors */
    var result = Component.extend({});
    return result;
});