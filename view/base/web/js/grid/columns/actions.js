define([
    'jquery',
    'Magento_Ui/js/grid/columns/actions',
    'mage/template',
    'text!Praxigento_Accounting/templates/modal/change_balance.html',
    'Magento_Ui/js/modal/modal'
], function ($, Actions, mageTemplate, innerHtml) {
    'use strict';

    /* save globals FORM_KEY into local context */
    var baseUrl = BASE_URL;
    var formKey = FORM_KEY;

    return Actions.extend({

        applyAction: function (actionIndex, rowIndex) {
            /* get action & row data*/
            var action = this.getAction(rowIndex, actionIndex);
            var row = this.rows[rowIndex];
            var accountId = row['id'];
            /* create modal dialog for current row (attributes are case sensitive) */
            var modalHtml = mageTemplate(
                innerHtml,
                {
                    formAction: 'changeBalance',
                    assetCode: row.asset,
                    balance: row.balance,
                    customerName: row.custName,
                    customerEmail: row.custEmail,
                    customerRef: row.ref,
                    accountId: row.id
                }
            );
            /* define function to send AJAX request to server */
            var fnSend = function () {
                var msg = $('#prxgt-msg');
                var input = $('#prxgt-change-balance-value');
                msg.text('Loading...');
                var value = input.val();
                var url = baseUrl + 'account/changeBalance/';
                var data = {changeValue: value, accountId: accountId};
                /* define function to process response from server */
                var fnSuccess = function (data, status, xhr) {
                    if (data.error) {
                        msg.text('Error: ' + data.message);
                    } else {
                        msg.text('Done. Please, close the dialog.');
                    }
                };
                var opts = {
                    data: data,
                    // contentType: 'application/json',
                    type: 'post',
                    success: fnSuccess
                };
                $.ajax(url, opts);
            };

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
                    }
                ]
            }).trigger('openModal');
            return this;
        }

    });
});