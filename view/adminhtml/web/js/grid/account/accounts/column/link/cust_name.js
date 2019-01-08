define([
    "Praxigento_Core/js/grid/column/link"
], function (Column) {
    "use strict";

    return Column.extend({
        defaults: {
            /** @see \Praxigento\Accounting\Ui\DataProvider\Grid\Account\Query::A_CUST_ID */
            idAttrName: "custId",
            route: "/customer/index/edit/id/"
        }
    });
});
