<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
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
    const ACCOUNTS_IDS = 'accountsIds';
    const ASSET_TYPE_CODES = 'assetTypeCodes';
    const ASSET_TYPE_IDS = 'assetTypeIds';
    const DATE_RESET_FROM = "dateResetFrom";
    const DAYS_TO_RESET = "daysToReset";

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
     * Timestamp to reset balances (incl.)
     * @return string
     */
    public function getDateResetFrom()
    {
        $result = $this->get(self::DATE_RESET_FROM);
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
     * Timestamp to reset balances (incl.)
     *
     * @param string $data
     * @return void
     */
    public function setDateResetFrom($data)
    {
        $this->set(self::DATE_RESET_FROM, $data);
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