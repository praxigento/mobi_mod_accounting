<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance\Calc;

/**
 * Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more preferable
 * then $assetTypeCode (if both are set). If asset type is not set then all asset types will be processed.
 *
 * Asset type attributes are ignored if accounts_ids attribute is not empty.
 */
class Request
    extends \Praxigento\Core\App\Service\Request
{
    const ACCOUNTS_IDS = 'accounts_ids';
    const ASSET_TYPE_CODES = 'asset_type_codes';
    const ASSET_TYPE_IDS = 'asset_type_ids';
    const DAYS_TO_RESET = "days_to_reset";

    /**
     * @return int[]
     */
    public function getAccountsIds()
    {
        $result = $this->get(self::ACCOUNTS_IDS);
        return $result;
    }

    /**
     * @return string[]
     */
    public function getAssetTypeCodes()
    {
        $result = $this->get(self::ASSET_TYPE_CODES);
        return $result;
    }

    /**
     * @return int[]
     */
    public function getAssetTypeIds()
    {
        $result = $this->get(self::ASSET_TYPE_IDS);
        return $result;
    }

    /**
     * @return int
     */
    public function getDaysToReset()
    {
        $result = $this->get(self::DAYS_TO_RESET);
        return $result;
    }

    /**
     * @param int[] $data
     * @return void
     */
    public function setAccountsIds($data)
    {
        $this->set(self::ACCOUNTS_IDS, $data);
    }

    /**
     * @param string[] $data
     * @return void
     */
    public function setAssetTypeCodes($data)
    {
        $this->set(self::ASSET_TYPE_CODES, $data);
    }

    /**
     * @param int[] $data
     * @return void
     */
    public function setAssetTypeIds($data)
    {
        $this->set(self::ASSET_TYPE_IDS, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setDaysToReset($data)
    {
        $this->set(self::DAYS_TO_RESET, $data);
    }
}