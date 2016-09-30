<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Transaction;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

use Praxigento\Accounting\Data\Agg\Transaction as Agg;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Mapper_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Agg\Mapper
{
    /** @var  Mapper */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Mapper();
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Mapper::class, $this->obj);
    }

    public function test_get()
    {
        /** === Call and asserts  === */
        $res = $this->obj->get(Agg::AS_ASSET);
        $this->assertEquals("pata.code", $res);
        $res = $this->obj->get(Agg::AS_CREDIT);
        $this->assertEquals("ce_cr.email", $res);
        $res = $this->obj->get(Agg::AS_DATE_APPLIED);
        $this->assertEquals("pat.date_applied", $res);
        $res = $this->obj->get(Agg::AS_DEBIT);
        $this->assertEquals("ce_db.email", $res);
        $res = $this->obj->get(Agg::AS_ID_OPER);
        $this->assertEquals("pat.operation_id", $res);
        $res = $this->obj->get(Agg::AS_ID_TRANS);
        $this->assertEquals("pat.id", $res);
        $res = $this->obj->get(Agg::AS_NOTE);
        $this->assertEquals("pat.note", $res);
        $res = $this->obj->get(Agg::AS_VALUE);
        $this->assertEquals("pat.value", $res);
    }
}