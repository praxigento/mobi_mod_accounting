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
    /** @var \Praxigento\Core\Repo\IBasic */
    protected $_repoBasic;

    /**
     * Account constructor.
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $rsrcConn,
        \Praxigento\Core\Repo\IBasic $repoBasic
    ) {
        parent::__construct($rsrcConn);
        $this->_repoBasic = $repoBasic;
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