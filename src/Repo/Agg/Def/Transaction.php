<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def;


class Transaction
    extends \Praxigento\Core\Repo\Def\Agg
    implements \Praxigento\Accounting\Repo\Agg\ITransaction
{
    /** TODO: move all constants from interfaces into implementations */
    const AS_ACC_CREDIT = 'paa_cr';
    const AS_ACC_DEBIT = 'paa_db';
    const AS_ASSET = 'pata';
    const AS_CUST_CREDIT = 'ce_cr';
    const AS_CUST_DEBIT = 'ce_db';
    const AS_TRANS = 'pat';

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        Transaction\SelectFactory $factorySelect
    ) {
        parent::__construct($resource, $factorySelect);
    }
}