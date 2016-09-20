<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def;


class Operation
    extends \Praxigento\Core\Repo\Def\Agg
    implements \Praxigento\Accounting\Repo\Agg\IOperation
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        Operation\SelectFactory $factorySelect
    ) {
        parent::__construct($resource, $factorySelect);
    }
}