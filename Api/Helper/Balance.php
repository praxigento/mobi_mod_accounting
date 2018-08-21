<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Api\Helper;

/**
 * Account balance related functionality.
 */
interface Balance
{

    /**
     * Get customer's current balance for given asset.
     *
     * @param int $custId
     * @param string $assetTypeCode
     * @return float current balance if found, 'null' - otherwise.
     */
    public function get($custId, $assetTypeCode);
}