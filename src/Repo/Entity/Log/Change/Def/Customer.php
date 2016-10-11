<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Log\Change\Def;

class Customer
    extends \Praxigento\Core\Repo\Entity\Def\Type
    implements \Praxigento\Accounting\Repo\Entity\Log\Change\ICustomer
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct(
            $resource,
            $repoGeneric,
            \Praxigento\Accounting\Data\Entity\Log\Change\Customer::class
        );
    }
}