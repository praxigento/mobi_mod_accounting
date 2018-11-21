<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\Calc;

/**
 * Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more preferable
 * then $assetTypeCode (if both are set). If asset type is not set then all asset types will be processed.
 */
class Request
    extends \Praxigento\Core\App\Service\Request
{
    const ASSET_TYPE_CODE = 'asset_type_code';
    const ASSET_TYPE_ID = 'asset_type_id';
    /** @deprecated */
    const DATE_TO = 'date_to';
    const DAYS_TO_RESET = "days_to_reset";

    /**
     * @return string
     */
    public function getAssetTypeCode()
    {
        $result = $this->get(static::ASSET_TYPE_CODE);
        return $result;
    }

    /**
     * @return int
     */
    public function getAssetTypeId()
    {
        $result = $this->get(static::ASSET_TYPE_ID);
        return $result;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getDateTo()
    {
        $result = $this->get(static::DATE_TO);
        return $result;
    }

    /**
     * @return int
     */
    public function getDaysToReset()
    {
        $result = $this->get(static::DAYS_TO_RESET);
        return $result;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setAssetTypeCode($data)
    {
        $this->set(static::ASSET_TYPE_CODE, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setAssetTypeId($data)
    {
        $this->set(static::ASSET_TYPE_ID, $data);
    }

    /**
     * @param string $data
     * @deprecated
     */
    public function setDateTo($data)
    {
        $this->set(static::DATE_TO, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setDaysToReset($data)
    {
        $this->set(static::DAYS_TO_RESET, $data);
    }
}