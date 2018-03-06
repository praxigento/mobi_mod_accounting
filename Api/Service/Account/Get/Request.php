<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Get;


class Request
    extends \Praxigento\Core\Data
{
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
     * Magento ID for customer.
     * @var int
     */
    const CUSTOMER_ID = 'customer_id';
    /**
     * Is Reperesentative Request
     * @var bool
     */
    const IS_REPRESENTATIVE = 'is_representative';

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
    public function setAssetTypeCode($data)
    {
        $this->set(self::ASSET_TYPE_CODE, $data);
    }

    /** @param int $data */
    public function setAssetTypeId($data)
    {
        $this->set(self::ASSET_TYPE_ID, $data);
    }

    /** @param int $data */
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