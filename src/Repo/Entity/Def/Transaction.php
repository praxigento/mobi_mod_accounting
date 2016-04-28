<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Transaction as Entity;
use Praxigento\Accounting\Repo\Entity\ITransaction;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;

class Transaction extends BaseEntityRepo implements ITransaction
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }
}