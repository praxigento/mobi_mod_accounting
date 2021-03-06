<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Data\Log\Change;

/**
 * Log for change balance operations performed by adminhtml users.
 */
class Admin
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
{
    const A_OPER_REF = 'operation_ref';
    const A_USER_REF = 'user_ref';
    const ENTITY_NAME = 'prxgt_acc_log_change_admin';

    /** @return int */
    public function getOperationRef()
    {
        $result = parent::get(self::A_OPER_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::A_OPER_REF];
    }

    /** @return int */
    public function getUserRef()
    {
        $result = parent::get(self::A_USER_REF);
        return $result;
    }

    /** @param int $data */
    public function setOperationRef($data)
    {
        parent::set(self::A_OPER_REF, $data);
    }

    /** @param int $data */
    public function setUserRef($data)
    {
        parent::set(self::A_USER_REF, $data);
    }

}