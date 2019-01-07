<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Accounting\Service\Account\Balance\Validate\A\ProcessOneAsset\A\Data;

/**
 * Data object to collect transaction related info.
 */
class Trans
{
    /**
     * @var int
     */
    public $accIdCredit;
    /**
     * @var int
     */
    public $accIdDebit;
    /**
     * @var float
     */
    public $amount;
}