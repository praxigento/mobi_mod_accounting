<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Operation\Request;

/**
 * @method string getDatePerformed()
 * @method void setDatePerformed(string $data)
 * @method string getOperationTypeCode()
 * @method void setOperationTypeCode(string $data)
 * @method int getOperationTypeId()
 * @method void setOperationTypeId(int $data)
 * @method array getTransactions()
 * @method void setTransactions(array $data)
 * @method string getAsTransRef() name of the field in Transactions data to bind new transaction ID to the field's value (bind transaction id with sale id or customer id)
 * @method void setAsTransRef(string $data)
 */
class Add extends \Praxigento\Core\Lib\Service\Base\Request {
}