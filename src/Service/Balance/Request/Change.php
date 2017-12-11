<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

/**
 * Classic definition for accessors is used in REST API data transformation (JSON <=> Data Objects).
 */
class Change
    extends \Praxigento\Core\App\Service\Base\Request
{
    /** @return int */
    public function getAdminUserId()
    {
        $result = parent::getAdminUserId();
        return $result;
    }

    /** @return float */
    public function getChangeValue()
    {
        $result = parent::getChangeValue();
        return $result;
    }

    /** @return int */
    public function getCustomerAccountId()
    {
        $result = parent::getCustomerAccountId();
        return $result;
    }

    /** @param int $data */
    public function setAdminUserId($data)
    {
        parent::setAdminUserId($data);
    }

    /** @param float $data */
    public function setChangeValue($data)
    {
        parent::setChangeValue($data);
    }

    /** @param int $data */
    public function setCustomerAccountId($data)
    {
        parent::setCustomerAccountId($data);
    }

}