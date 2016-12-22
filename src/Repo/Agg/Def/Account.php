<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def;

class Account
    extends \Praxigento\Core\Repo\Def\Agg
    implements \Praxigento\Accounting\Repo\Agg\IAccount
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        Account\SelectFactory $factorySelect
    ) {
        parent::__construct($resource, $factorySelect);
    }

}