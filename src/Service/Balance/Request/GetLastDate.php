<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

class GetLastDate extends \Praxigento\Core\Service\Base\Request
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

    public function getAssetTypeCode()
    {
        $result = $this->getData(static::ASSET_TYPE_CODE);
        return $result;
    }

    public function getAssetTypeId()
    {
        $result = $this->getData(static::ASSET_TYPE_ID);
        return $result;
    }

    public function setAssetTypeId($data)
    {
        $this->setData(static::ASSET_TYPE_ID, $data);
    }

    public function setAssetTypeCode($data)
    {
        $this->setData(static::ASSET_TYPE_CODE, $data);
    }
}