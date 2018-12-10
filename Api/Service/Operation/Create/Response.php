<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Operation\Create;

/**
 * @method int getOperationId()
 * @method void setOperationId(int $data)
 * @method array getTransactionsIds() [$transId, ...] or [$transId => $ref, ...]
 * @method void setTransactionsIds(array $data)
 */
class Response
    extends \Praxigento\Core\App\Service\Response
{
    const ERR_ONE_ACCOUNT_FOR_DEBIT_AND_CREDIT = 'one_account_for_debit_and_credit';
}