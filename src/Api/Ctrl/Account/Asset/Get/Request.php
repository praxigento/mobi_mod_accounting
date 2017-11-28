<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Ctrl\Account\Asset\Get;

/**
 * Request to get asset data for customer.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\App\WebApi\Request
{
    const ASSET_CODE = 'assetCode';
    const ASSET_ID = 'assetId';
    const CUSTOMER_ID = 'customerId';

    /**
     * @return string|null
     */
    public function getAssetCode()
    {
        $result = parent::get(self::ASSET_CODE);
        return $result;
    }

    /**
     * @return int|null
     */
    public function getAssetId()
    {
        $result = parent::get(self::ASSET_ID);
        return $result;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        $result = parent::get(self::CUSTOMER_ID);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setAssetCode($data)
    {
        parent::set(self::ASSET_CODE, $data);
    }

    /**
     * @param int $data
     */
    public function setAssetId($data)
    {
        parent::set(self::ASSET_ID, $data);
    }

    /**
     * @param int $data
     */
    public function setCustomerId($data)
    {
        parent::set(self::ASSET_ID, $data);
    }
}