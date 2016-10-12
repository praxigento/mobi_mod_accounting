<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Def;

/**
 * Class Balance
 * @package Praxigento\Accounting\Api\Def
 */
class Balance
    implements \Praxigento\Accounting\Api\BalanceInterface
{
    public function change($changeValue, $form_key)
    {
        $args = func_get_args();
        $result = new \Praxigento\Core\Service\Base\Response();
        $result->markSucceed();
        return $result;
    }

}