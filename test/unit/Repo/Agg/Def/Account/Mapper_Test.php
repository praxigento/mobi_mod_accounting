<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Account;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

use Praxigento\Accounting\Data\Agg\Account as Agg;

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
        $this->assertEquals('pata.code', $res);
        $res = $this->obj->get(Agg::AS_CUST_NAME);
        $this->assertInstanceOf(\Praxigento\Core\Repo\Query\Expression::class, $res);
        $this->assertEquals("(CONCAT(ce.firstname, ' ', ce.lastname))", (string)$res);
        $res = $this->obj->get(Agg::AS_CUST_EMAIL);
        $this->assertEquals('ce.email', $res);
        $res = $this->obj->get(Agg::AS_BALANCE);
        $this->assertEquals('paa.balance', $res);
        $res = $this->obj->get(Agg::AS_ID);
        $this->assertEquals('paa.id', $res);
    }
}