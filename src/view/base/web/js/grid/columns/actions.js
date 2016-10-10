define([
    'Magento_Ui/js/grid/columns/actions',
    'jquery',
    'mage/template',
    'text!Praxigento_Accounting/templates/modal/change_balance.html',
    'Magento_Ui/js/modal/modal'
], function (Actions, $, mageTemplate, changeBalanceTemplate) {
    'use strict';

    return Actions.extend({

        applyAction: function (actionIndex, rowIndex) {
            // debugger;
            /* get action data */
            var action = this.getAction(rowIndex, actionIndex);
            var row = this.rows[rowIndex];

            /* create modal dialog */
            var modalHtml = mageTemplate(
                changeBalanceTemplate,
                {
                    formAction: 'submit/to/some/route',
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
                buttons: []
            }).trigger('openModal');

            return this;
        }

    });
});