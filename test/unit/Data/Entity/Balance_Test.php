<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity\Data;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');
use Praxigento\Accounting\Repo\Entity\Data\Balance as DataEntity;

class Balance_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
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
        $ACC_ID = 'account_id';
        $CLOSE = 'close';
        $OPEN = 'open';
        $DATE = 'date';
        $CREDIT = 'credit';
        $DEBIT = 'debit';
        /** === Call and asserts  === */
        $this->obj->setAccountId($ACC_ID);
        $this->obj->setBalanceClose($CLOSE);
        $this->obj->setBalanceOpen($OPEN);
        $this->obj->setDate($DATE);
        $this->obj->setTotalCredit($CREDIT);
        $this->obj->setTotalDebit($DEBIT);
        $this->assertEquals($ACC_ID, $this->obj->getAccountId());
        $this->assertEquals($CLOSE, $this->obj->getBalanceClose());
        $this->assertEquals($OPEN, $this->obj->getBalanceOpen());
        $this->assertEquals($DATE, $this->obj->getDate());
        $this->assertEquals($CREDIT, $this->obj->getTotalCredit());
        $this->assertEquals($DEBIT, $this->obj->getTotalDebit());
    }

    public function test_pk()
    {
        /** === Call and asserts  === */
        $pk = $this->obj->getPrimaryKeyAttrs();
        $this->assertEquals([DataEntity::ATTR_ACCOUNT_ID, DataEntity::ATTR_DATE], $pk);
    }
}