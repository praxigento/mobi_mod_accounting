define([
    'jquery',
    'Magento_Ui/js/grid/columns/actions',
    'mage/template',
    'text!Praxigento_Accounting/templates/modal/change_balance.html',
    'Magento_Ui/js/modal/modal'
], function ($, Actions, mageTemplate, innerHtml) {
    'use strict';

    /* get base URL: http://host.com/admin/accounts/ => http://host.com */
    var parts = BASE_URL.split('/');
    var removed = parts.splice(-3, 3);
    var baseUrl = parts.join('/');
    /* save FROM_KEY */
    var formKey = FORM_KEY;

    var fnSend = function () {
        debugger;
        $('#prxgt-spinner').show();
        var input = $('#prxgt-change-balance-value');
        var value = input.val();
        var url = baseUrl + '/rest//V1/prxgt/acc/balance/change/';
        var data = JSON.stringify({changeValue: value});
        var opts = {
            data: data,
            contentType: 'application/json',
            type: 'post'
        };
        $.ajax(url, opts);

        var fnSuccess = function (data, status, xhr) {
            debugger;
            $('#prxgt-spinner').hide();
        }
    }

    return Actions.extend({

        applyAction: function (actionIndex, rowIndex) {
            // debugger;
            /* get action & row data*/
            var action = this.getAction(rowIndex, actionIndex);
            var row = this.rows[rowIndex];
            /* create modal dialog for current row */
            var modalHtml = mageTemplate(
                innerHtml,
                {
                    formAction: 'changeBalance',
                    assetCode: row.Asset,
                    balance: row.Balance,
                    customerName: row.CustName,
                    customerEmail: row.CustEmail,
                    customerRef: row.Ref,
                    accountId: row.Id,
                    // linkText: $.mage.__('Go to Details Page')
                }
            );

            /* display modal dialog */
            var previewPopup = $('<div/>').html(modalHtml);
            previewPopup.modal({
                title: 'Change balance',
                innerScroll: true,
                modalClass: '_image-box',
                buttons: [
                    {
                        text: "OK",
                        click: fnSend
                    },
                    {
                        text: "Cancel",
                        click: function () {
                            $('#prxgt-spinner').hide();
                        }
                    }
                ]
            }).trigger('openModal');

            $('#prxgt-spinner').hide();
            return this;
        }

    });
});