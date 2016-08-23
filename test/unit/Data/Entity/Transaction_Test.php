<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Data\Entity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');
use Praxigento\Accounting\Data\Entity\Transaction as DataEntity;

class Transaction_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  DataEntity */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->obj = new DataEntity();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CREDIT_ACC_ID = 'credit_acc_id';
        $DATE_APPLIED = 'date_applied';
        $DEBIT_ACC_ID = 'debit_acc_id';
        $ID = 'id';
        $OPERATION_ID = 'operation_id';
        $VALUE = 'value';
        /** === Call and asserts  === */
        $this->obj->setCreditAccId($CREDIT_ACC_ID);
        $this->obj->setDateApplied($DATE_APPLIED);
        $this->obj->setDebitAccId($DEBIT_ACC_ID);
        $this->obj->setId($ID);
        $this->obj->setOperationId($OPERATION_ID);
        $this->obj->setValue($VALUE);
        $this->assertEquals($CREDIT_ACC_ID, $this->obj->getCreditAccId());
        $this->assertEquals($DATE_APPLIED, $this->obj->getDateApplied());
        $this->assertEquals($DEBIT_ACC_ID, $this->obj->getDebitAccId());
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($OPERATION_ID, $this->obj->getOperationId());
        $this->assertEquals($VALUE, $this->obj->getValue());
    }

    public function test_pk()
    {
        /** === Call and asserts  === */
        $pk = $this->obj->getPrimaryKeyAttrs();
        $this->assertEquals([DataEntity::ATTR_ID], $pk);
    }
}