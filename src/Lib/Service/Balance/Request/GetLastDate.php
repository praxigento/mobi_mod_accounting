<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Balance\Request;

class GetLastDate extends \Praxigento\Core\Lib\Service\Base\Request {
    /**
     * ID of the account's asset type. Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more
     * preferable then $assetTypeCode (if both are set).
     * @var int
     */
    const ASSET_TYPE_ID = 'asset_type_id';
    /**
     * Code of the account's asset type. Use $accountTypeId or $assetTypeCode to set asset type. $assetTypeId is more
     * preferable then $assetTypeCode (if both are set).
     * @var string
     */
    const ASSET_TYPE_CODE = 'asset_type_code';
}