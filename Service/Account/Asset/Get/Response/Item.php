<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Account\Asset\Get\Response;

class Item
    extends \Praxigento\Core\Data
{
    const ACC_BALANCE = 'accBalance';
    const ACC_ID = 'accId';
    const ASSET_CODE = 'assetCode';
    const ASSET_ID = 'assetId';

    /**
     * @return float|null
     */
    public function getAccBalance() {
        $result = parent::get(self::ACC_BALANCE);
        return $result;
    }

    /**
     * @return int|null
     */
    public function getAccId() {
        $result = parent::get(self::ACC_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getAssetCode() {
        $result = parent::get(self::ASSET_CODE);
        return $result;
    }

    /**
     * @return int
     */
    public function getAssetId() {
        $result = parent::get(self::ASSET_ID);
        return $result;
    }

    /**
     * @param float $data
     */
    public function setAccBalance($data) {
        parent::set(self::ACC_BALANCE, $data);
    }

    /**
     * @param int $data
     */
    public function setAccId($data) {
        parent::set(self::ACC_ID, $data);
    }

    /**
     * @param string $data
     */
    public function setAssetCode($data) {
        parent::set(self::ASSET_CODE, $data);
    }

    /**
     * @param int $data
     */
    public function setAssetId($data) {
        parent::set(self::ASSET_ID, $data);
    }

}