<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Type\Def;

use Praxigento\Accounting\Data\Entity\Type\Asset;
use Praxigento\Core\Lib\Context;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Asset_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\Def\Asset */
    private $obj;
    /** @var  \Mockery\MockInterface */
    private $mDba;
    /** @var  \Mockery\MockInterface */
    private $mRsrcConn;

    public function setUp()
    {
        parent::setUp();
        $this->mDba = $this->_mockDba();
        $this->mRsrcConn = $this->_mockResourceConnection($this->mDba);
        $this->obj = new \Praxigento\Accounting\Repo\Entity\Type\Def\Asset($this->mRsrcConn);
    }

    public function test_install()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        // $tbl = $this->_dba->getTableName($entity);
        $this->mDba
            ->shouldReceive('getTableName')
            ->with(Asset::ENTITY_NAME);
        // $query = $this->_dba->select();
        $mQuery = $this->_mockDbSelect();
        $this->mDba
            ->shouldReceive('select')
            ->andReturn($mQuery);
        //  $query->from($tbl);
        // $query->where(EntityTypeBase::ATTR_CODE . '=:code');
        $mQuery->shouldReceive('from', 'where');
        // $data = $this->_dba->fetchRow($query, ['code' => $code]);
        $this->mDba->shouldReceive('fetchRow');
        /* === Call and asserts  === */
        $this->obj->getIdByCode('code');
    }
}