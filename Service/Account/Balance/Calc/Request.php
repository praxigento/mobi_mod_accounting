<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc;

class Request
    extends \Praxigento\Core\App\Service\Base\Request
{
    /**
     * Code of the account's asset type. Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more
     * preferable then $assetTypeCode (if both are set).
     * @var string
     */
    const ASSET_TYPE_CODE = 'asset_type_code';
    /**
     * ID of the account's asset type.
     * @var int
     */
    const ASSET_TYPE_ID = 'asset_type_id';
    /**
     * Calculate balances up to this date (including).
     *
     * @var  string datestamp (YYYYMMDD).
     */
    const DATE_TO = 'date_to';

    public function getAssetTypeCode()
    {
        $result = $this->get(static::ASSET_TYPE_CODE);
        return $result;
    }

    public function getAssetTypeId()
    {
        $result = $this->get(static::ASSET_TYPE_ID);
        return $result;
    }

    public function getDateTo()
    {
        $result = $this->get(static::DATE_TO);
        return $result;
    }

    public function setAssetTypeCode($data)
    {
        $this->set(static::ASSET_TYPE_CODE, $data);
    }

    public function setAssetTypeId($data)
    {
        $this->set(static::ASSET_TYPE_ID, $data);
    }

    public function setDateTo($data)
    {
        $this->set(static::DATE_TO, $data);
    }
}