<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api;

/**
 * Interface BalanceInterface
 * @package Praxigento\Accounting\Api
 */
interface BalanceInterface
{
    /**
     * @param float $changeValue
     * @param int $accountId
     * @param string $form_key
     * @return \Praxigento\Core\Service\Base\Response
     */
    public function change($changeValue, $accountId, $form_key);
}