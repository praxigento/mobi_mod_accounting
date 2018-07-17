<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Asset\Transfer;

/**
 * Request to process asset transfer between accounts.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\Data
{
    const AMOUNT = 'amount';
    const ASSET_ID = 'assetId';
    const COUNTER_PARTY_ID = 'counterPartyId';
    const CUSTOMER_ID = 'customerId';
    const DATE_APPLIED = 'dateApplied';
    const IS_DIRECT = 'isDirect';
    const NOTE = 'note';
    const USER_ID = 'userId';

    /**
     * @return float
     */
    public function getAmount()
    {
        $result = parent::get(self::AMOUNT);
        return $result;
    }

    /**
     * @return int
     */
    public function getAssetId()
    {
        $result = parent::get(self::ASSET_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getCounterPartyId()
    {
        $result = parent::get(self::COUNTER_PARTY_ID);
        return $result;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        $result = parent::get(self::CUSTOMER_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getDateApplied()
    {
        $result = parent::get(self::DATE_APPLIED);
        return $result;
    }

    /**
     * @return bool
     */
    public function getIsDirect()
    {
        $result = parent::get(self::IS_DIRECT);
        return $result;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        $result = parent::get(self::NOTE);
        return $result;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        $result = parent::get(self::USER_ID);
        return $result;
    }

    /**
     * @param float $data
     */
    public function setAmount($data)
    {
        parent::set(self::AMOUNT, $data);
    }

    /**
     * @param int $data
     */
    public function setAssetId($data)
    {
        parent::set(self::ASSET_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setCounterPartyId($data)
    {
        parent::set(self::COUNTER_PARTY_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::CUSTOMER_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setDateApplied($data)
    {
        parent::set(self::DATE_APPLIED, $data);
    }

    /**
     * @param bool $data
     */
    public function setIsDirect($data)
    {
        parent::set(self::IS_DIRECT, $data);
    }

    /**
     * @param string $data
     */
    public function setNote($data)
    {
        parent::set(self::NOTE, $data);
    }

    /**
     * @param int $data
     */
    public function setUserId($data)
    {
        parent::set(self::USER_ID, $data);
    }
}