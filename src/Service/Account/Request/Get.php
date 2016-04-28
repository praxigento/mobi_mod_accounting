<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account\Request;

class Get extends \Praxigento\Core\Service\Base\Request {
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

    public function setAccountId($data) {
        $this->setData(self::ACCOUNT_ID, $data);
    }

    public function setAssetTypeCode($data) {
        $this->setData(self::ASSET_TYPE_CODE, $data);
    }

    public function setAssetTypeId($data) {
        $this->setData(self::ASSET_TYPE_ID, $data);
    }

    public function setCreateNewAccountIfMissed($data = true) {
        $this->setData(self::CREATE_NEW_ACCOUNT_IF_MISSED, $data);
    }

    public function setCustomerId($data) {
        $this->setData(self::CUSTOMER_ID, $data);
    }

    public function getAccountId() {
        $result = $this->getData(self::ACCOUNT_ID);
        return $result;
    }

    public function getAssetTypeCode() {
        $result = $this->getData(self::ASSET_TYPE_CODE);
        return $result;
    }

    public function getAssetTypeId() {
        $result = $this->getData(self::ASSET_TYPE_ID);
        return $result;
    }

    public function getCreateNewAccountIfMissed() {
        $result = $this->getData(self::CREATE_NEW_ACCOUNT_IF_MISSED);
        return $result;
    }

    public function getCustomerId() {
        $result = $this->getData(self::CUSTOMER_ID);
        return $result;
    }
}