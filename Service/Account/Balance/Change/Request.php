<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\Change;


class Request
    extends \Praxigento\Core\App\Service\Request
{
    const ADMIN_USER_ID = 'adminUserId';
    const CHANGE_VALUE = 'changeValue';
    const CUSTOMER_ACCOUNT_ID = 'customerAccountId';

    /** @return int */
    public function getAdminUserId()
    {
        $result = parent::get(self::ADMIN_USER_ID);
        return $result;
    }

    /** @return float */
    public function getChangeValue()
    {
        $result = parent::get(self::CHANGE_VALUE);
        return $result;
    }

    /** @return int */
    public function getCustomerAccountId()
    {
        $result = parent::get(self::CUSTOMER_ACCOUNT_ID);
        return $result;
    }

    /** @param int $data */
    public function setAdminUserId($data)
    {
        parent::set(self::ADMIN_USER_ID, $data);
    }

    /** @param float $data */
    public function setChangeValue($data)
    {
        parent::set(self::CHANGE_VALUE, $data);
    }

    /** @param int $data */
    public function setCustomerAccountId($data)
    {
        parent::set(self::CUSTOMER_ACCOUNT_ID, $data);
    }
}
