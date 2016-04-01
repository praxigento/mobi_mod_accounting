<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Entity;


use Praxigento\Core\Data\Entity\Base as EntityBase;

class Operation extends EntityBase
{
    const ATTR_DATE_PREFORMED = 'date_performed';
    const ATTR_ID = 'id';
    const ATTR_TYPE_ID = 'type_id';
    const ENTITY_NAME = 'prxgt_acc_operation';

    /**
     * @return string
     */
    public function getDatePerformed()
    {
        $result = parent::getData(self::ATTR_DATE_PREFORMED);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::getData(self::ATTR_ID);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ID];
    }

    /**
     * @return int
     */
    public function getTypeId()
    {
        $result = parent::getData(self::ATTR_TYPE_ID);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setDatePerformed($data)
    {
        parent::setData(self::ATTR_DATE_PREFORMED, $data);
    }

    /**
     * @param int $data
     */
    public function setId($data)
    {
        parent::setData(self::ATTR_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setTypeId($data)
    {
        parent::setData(self::ATTR_TYPE_ID, $data);
    }
}