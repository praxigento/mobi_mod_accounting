<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Type\Def;

use Praxigento\Accounting\Data\Entity\Type\Operation as Entity;
use Praxigento\Accounting\Repo\Entity\Type\IOperation;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Operation_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Operation */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /** create object to test */
        $mResource = $this->_mockResourceConnection($this->mConn);
        $this->obj = new Operation(
            $mResource,
            $this->mRepoGeneric,
            Entity::class
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(IOperation::class, $this->obj);
    }
}