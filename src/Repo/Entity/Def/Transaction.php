<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Transaction as EntityTransaction;
use Praxigento\Accounting\Repo\Entity\ITransaction;
use Praxigento\Core\Repo\Def\Base;

class Transaction extends Base implements ITransaction
{
    /** @var \Praxigento\Core\Repo\IBasic */
    protected $_repoBasic;

    /**
     * Account constructor.
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IBasic $repoBasic
    ) {
        parent::__construct($resource);
        $this->_repoBasic = $repoBasic;
    }

    public function create($data)
    {
        $result = null;
        $entity = EntityTransaction::ENTITY_NAME;
        $id = $this->_repoBasic->addEntity($entity, $data);
        if ($id) {
            $result = $data;
            $result[EntityTransaction::ATTR_ID] = $id;
        }
        return $result;
    }

}