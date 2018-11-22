<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance\LastDate;

class Request
    extends \Praxigento\Core\App\Service\Request
{
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

    public function getAccountId()
    {
        $result = $this->get(self::ACCOUNT_ID);
        return $result;
    }

    public function getAssetTypeCode()
    {
        $result = $this->get(self::ASSET_TYPE_CODE);
        return $result;
    }

    public function getAssetTypeId()
    {
        $result = $this->get(self::ASSET_TYPE_ID);
        return $result;
    }

    public function setAccountId($data)
    {
        $this->set(self::ACCOUNT_ID, $data);
    }

    public function setAssetTypeCode($data)
    {
        $this->set(self::ASSET_TYPE_CODE, $data);
    }

    public function setAssetTypeId($data)
    {
        $this->set(self::ASSET_TYPE_ID, $data);
    }
}
