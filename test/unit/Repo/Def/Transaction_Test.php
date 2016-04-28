<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Transaction as EntityData;
use Praxigento\Accounting\Repo\Entity\ITransaction;


include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Transaction_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  \Mockery\MockInterface */
    private $mRsrcConn;
    /** @var  Transaction */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        $this->mRsrcConn = $this->_mockResourceConnection($this->mDba);
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        $this->obj = new Transaction(
            $this->mRsrcConn,
            $this->mRepoGeneric,
            EntityData::class
        );
    }

    public function test_constructor()
    {
        /* === Call and asserts  === */
        $this->assertInstanceOf(ITransaction::class, $this->obj);
    }

}