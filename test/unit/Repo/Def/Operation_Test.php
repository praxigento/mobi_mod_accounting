<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Operation as EntityData;
use Praxigento\Accounting\Repo\Entity\IOperation;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Operation_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  \Mockery\MockInterface */
    private $mRsrcConn;
    /** @var  Operation */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->mRsrcConn = $this->_mockResourceConnection($this->mDba);
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->obj = new Operation(
            $this->mRsrcConn,
            $this->mRepoGeneric,
            EntityData::class
        );
    }

    public function test_constructor()
    {
        /* === Call and asserts  === */
        $this->assertInstanceOf(IOperation::class, $this->obj);
    }

}