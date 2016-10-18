<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Operation\Request;

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
 * @method int getCustomerId() if set then new log record will be added to Customer Log
 * @method void setCustomerId(int $data)
 * @method int getAdminUserId() if set then new log record will be added to Admin User Log
 * @method void setAdminUserId(int $data)
 */
class Add extends \Praxigento\Core\Service\Base\Request
{
}