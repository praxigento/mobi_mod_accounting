<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Operation;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

use Praxigento\Accounting\Data\Agg\Operation as Agg;

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
        $res = $this->obj->get(Agg::AS_DATE_PERFORMED);
        $this->assertEquals("pao.date_performed", $res);
        $res = $this->obj->get(Agg::AS_ID);
        $this->assertEquals("pao.id", $res);
        $res = $this->obj->get(Agg::AS_NOTE);
        $this->assertEquals("pao.note", $res);
        $res = $this->obj->get(Agg::AS_TYPE);
        $this->assertEquals("pato.code", $res);
    }
}