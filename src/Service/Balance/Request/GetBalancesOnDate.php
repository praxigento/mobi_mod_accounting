<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

class GetBalancesOnDate extends \Praxigento\Core\Service\Base\Request {
    /**
     * Date as datestamp (YYYYMMDD).
     *
     * @var  string datestamp (YYYYMMDD).
     */
    const DATE = 'date';
    /**
     * ID of the account's asset type.
     * @var int
     */
    const ASSET_TYPE_ID = 'asset_type_id';
}