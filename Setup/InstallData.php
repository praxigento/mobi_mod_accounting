<?php
/**
 * Populate DB schema with module's initial data
 * .
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Setup;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Repo\Entity\Data\Type\Operation as TypeOperation;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class InstallData extends \Praxigento\Core\App\Setup\Data\Base
{

    private function _addAccountingOperationsTypes()
    {
        $this->_conn->insertArray(
            $this->_resource->getTableName(TypeOperation::ENTITY_NAME),
            [TypeOperation::ATTR_CODE, TypeOperation::ATTR_NOTE],
            [
                [Cfg::CODE_TYPE_OPER_CHANGE_BALANCE, 'Change account balance by operator (adminhtml).']
            ]
        );
    }

    protected function _setup()
    {
        $this->_addAccountingOperationsTypes();
    }
}