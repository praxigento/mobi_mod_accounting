<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Data;

class Operation
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_DATE_PREFORMED = 'date_performed';
    const A_ID = 'id';
    const A_NOTE = 'note';
    const A_TYPE_ID = 'type_id';
    const ENTITY_NAME = 'prxgt_acc_operation';

    /** @return string */
    public function getDatePerformed()
    {
        $result = parent::get(self::A_DATE_PREFORMED);
        return $result;
    }

    /** @return int */
    public function getId()
    {
        $result = parent::get(self::A_ID);
        return $result;
    }

    /** @return string */
    public function getNote()
    {
        $result = parent::get(self::A_NOTE);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_ID];
    }

    /** @return int */
    public function getTypeId()
    {
        $result = parent::get(self::A_TYPE_ID);
        return $result;
    }

    /** @param string $data */
    public function setDatePerformed($data)
    {
        parent::set(self::A_DATE_PREFORMED, $data);
    }

    /** @param int $data */
    public function setId($data)
    {
        parent::set(self::A_ID, $data);
    }

    /** @param string $data */
    public function setNote($data)
    {
        parent::set(self::A_NOTE, $data);
    }

    /** @param int $data */
    public function setTypeId($data)
    {
        parent::set(self::A_TYPE_ID, $data);
    }
}