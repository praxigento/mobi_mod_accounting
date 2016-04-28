<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

class Calc extends \Praxigento\Core\Service\Base\Request {
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
}