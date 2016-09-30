<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Operation_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo
{

    /** @var  Operation */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mFactorySelect = $this->_mock(Operation\SelectFactory::class);
        /** create object to test */
        $this->obj = new Operation(
            $this->mResource,
            $this->mFactorySelect
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Operation::class, $this->obj);
    }
}