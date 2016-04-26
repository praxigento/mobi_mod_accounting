<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Operation as EntityOperation;
use Praxigento\Accounting\Repo\Entity\IOperation;
use Praxigento\Core\Repo\Def\Base;

class Operation extends Base implements IOperation
{
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoBasic;

    /**
     * Account constructor.
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource);
        $this->_repoBasic = $repoGeneric;
    }

    public function create($data)
    {
        $result = null;
        $entity = EntityOperation::ENTITY_NAME;
        $id = $this->_repoBasic->addEntity($entity, $data);
        if ($id) {
            $result = $data;
            $result[EntityOperation::ATTR_ID] = $id;
        }
        return $result;
    }

}