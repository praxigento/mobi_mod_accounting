<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Account\Request;

/**
 * Use AssetTypeId or AssetTypeCode to set asset type. AssetTypeId is more preferable then AssetTypeCode (if both
 * are set).
 *
 * @method string getAssetTypeCode() Code of the account's asset type.
 * @method void setAssetTypeCode(string $data)
 * @method int getAssetTypeId() ID of the account's asset type.
 * @method void setAssetTypeId(int $data)
 */
class GetRepresentative extends \Praxigento\Core\Service\Base\Request {
}