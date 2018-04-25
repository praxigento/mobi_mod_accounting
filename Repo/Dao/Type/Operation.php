<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao\Type;

class Operation
    extends \Praxigento\Core\App\Repo\Dao\Type
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    ) {
        parent::__construct(
            $resource,
            $daoGeneric,
            \Praxigento\Accounting\Repo\Data\Type\Operation::class
        );
    }

}