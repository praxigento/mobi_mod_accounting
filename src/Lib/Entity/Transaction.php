<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Entity;

class Transaction {
    const ATTR_CREDIT_ACC_ID = 'credit_acc_id';
    /* date when asset transaction should change balances (can be in the past) */
    const ATTR_DATE_APPLIED = 'date_applied';
    const ATTR_DEBIT_ACC_ID = 'debit_acc_id';
    const ATTR_ID = 'id';
    const ATTR_OPERATION_ID = 'operation_id';
    const ATTR_VALUE = 'value';
    const ENTITY_NAME = 'prxgt_acc_transaction';
}