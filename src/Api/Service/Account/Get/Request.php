<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Get;


class Request
    extends \Praxigento\Core\Data
{
    /**
     * ID of the account. $accountId has the highest priority for identity.
     * @var int
     */
    const ACCOUNT_ID = 'account_id';
    /**
     * Code of the account's asset type. Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more
     * preferable then $assetTypeCode (if both are set).
     * @var string
     */
    const ASSET_TYPE_CODE = 'asset_type_code';
    /**
     * ID of the account's asset type. Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more
     * preferable then $assetTypeCode (if both are set).
     * @var int
     */
    const ASSET_TYPE_ID = 'asset_type_id';
    /**
     * Set 'true' if new account should be created for customer.
     * @var bool
     */
    const CREATE_NEW_ACCOUNT_IF_MISSED = 'create_new_account_if_missed';
    /**
     * Magento ID for customer.
     * @var int
     */
    const CUSTOMER_ID = 'customer_id';
    /**
     * Is Reperesentative Request
     * @var bool
     */
    const IS_REPRESENTATIVE = 'is_representative';

    /** @return int */
    public function getAccountId()
    {
        $result = $this->get(self::ACCOUNT_ID);
        return $result;
    }

    /** @return string */
    public function getAssetTypeCode()
    {
        $result = $this->get(self::ASSET_TYPE_CODE);
        return $result;
    }

    /** @return int */
    public function getAssetTypeId()
    {
        $result = $this->get(self::ASSET_TYPE_ID);
        return $result;
    }

    /** @return bool */
    public function getCreateNewAccountIfMissed()
    {
        $result = $this->get(self::CREATE_NEW_ACCOUNT_IF_MISSED);
        return $result;
    }

    /** @return int */
    public function getCustomerId()
    {
        $result = $this->get(self::CUSTOMER_ID);
        return $result;
    }

    /** @return bool */
    public function getIsRepresentative()
    {
        return $this->get(self::IS_REPRESENTATIVE);
    }

    /** @param string $data */
    public function setAccountId($data)
    {
        $this->set(self::ACCOUNT_ID, $data);
    }

    /** @param string $data */
    public function setAssetTypeCode($data)
    {
        $this->set(self::ASSET_TYPE_CODE, $data);
    }

    /** @param string $data */
    public function setAssetTypeId($data)
    {
        $this->set(self::ASSET_TYPE_ID, $data);
    }

    /** @param bool $data */
    public function setCreateNewAccountIfMissed($data)
    {
        $this->set(self::CREATE_NEW_ACCOUNT_IF_MISSED, $data);
    }

    /** @param string $data */
    public function setCustomerId($data)
    {
        $this->set(self::CUSTOMER_ID, $data);
    }

    /** @param bool $data */
    public function setIsRepresentative($data)
    {
        $this->set(self::IS_REPRESENTATIVE, $data);
    }
}