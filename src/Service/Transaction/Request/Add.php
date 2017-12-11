<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Transaction\Request;

/**
 * @method int getCreditAccId()
 * @method void setCreditAccId(int $data)
 * @method string getDateApplied() timestamp like '2016-01-24 12:34:45'
 * @method void setDateApplied(string $data)
 * @method int getDebitAccId()
 * @method void setDebitAccId(int $data)
 * @method string|null getNote()
 * @method void setNote(string $data)
 * @method int getOperationId()
 * @method void setOperationId(int $data)
 * @method number getValue()
 * @method void setValue(number $data)
 */
class Add extends \Praxigento\Core\App\Service\Base\Request {

}