<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Repo\Query\Balance\OnDate;


interface Closing
{
    /**
     * Tables aliases.
     */
    const AS_ACC = 'acc';
    const AS_BALANCE = 'bal';
    const AS_DATE_MAX = 'dateMax'; // alias for JOINed query (see \...\Repo\Query\Balance\OnDate\Closing\A\MaxDates)

    /**
     * Attributes aliases.
     */
    const A_ACC_ID = 'accId';
    const A_BALANCE = 'balance';
    const A_CUST_ID = 'custId';
    const A_DATE_MAX = 'dateMax';

    /** Bound variables names */
    const BND_MAX_DATE = 'balanceDateMax'; // see \...\Repo\Query\Balance\OnDate\Closing\A\MaxDates

    /**
     * @param \Magento\Framework\DB\Select|null $source
     * @return \Magento\Framework\DB\Select
     */
    public function build(\Magento\Framework\DB\Select $source = null);
}