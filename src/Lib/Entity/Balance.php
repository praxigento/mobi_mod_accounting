<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Entity;

class Balance implements \Praxigento\Core\Lib\IEntity
{
    const ATTR_ACCOUNT_ID = 'account_id';
    const ATTR_BALANCE_CLOSE = 'closing_balance';
    const ATTR_BALANCE_OPEN = 'opening_balance';
    const ATTR_DATE = 'date';
    const ATTR_TOTAL_CREDIT = 'total_credit';
    const ATTR_TOTAL_DEBIT = 'total_debit';
    const ENTITY_NAME = 'prxgt_acc_balance';

    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_ACCOUNT_ID, self::ATTR_DATE];
    }
}