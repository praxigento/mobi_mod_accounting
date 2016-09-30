<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Ui\DataProvider;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Operation_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Ui\DataProvider
{
    /** @var  \Mockery\MockInterface */
    private $mMapperApi2Sql;
    /** @var  \Mockery\MockInterface */
    private $mRepo;
    /** @var  Operation */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mMapperApi2Sql = $this->_mock(\Praxigento\Accounting\Repo\Agg\Def\Operation\Mapper::class);
        $this->mRepo = $this->_mock(\Praxigento\Accounting\Repo\Agg\IOperation::class);
        /** create object to test */
        $this->obj = new Operation(
            $this->mUrl,
            $this->mCritAdapter,
            $this->mMapperApi2Sql,
            $this->mRepo,
            $this->mReporting,
            $this->mSearchCritBuilder,
            $this->mRequest,
            $this->mFilterBuilder,
            'name'
        );
    }


    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Operation::class, $this->obj);
    }

}