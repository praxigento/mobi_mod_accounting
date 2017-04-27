<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Sub;

use Praxigento\Accounting\Data\Entity\Balance;
use Praxigento\Accounting\Data\Entity\Transaction;
use Praxigento\Accounting\Service\Balance\Sub;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class CalcSimple_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mToolPeriod;
    /** @var  CalcSimple */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mToolPeriod = $this->_mock(\Praxigento\Core\Tool\IPeriod::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new CalcSimple(
            $this->mToolPeriod
        );
    }

    public function test_calcBalances()
    {
        /** === Test Data === */
        $ACC_FROM = 12;
        $ACC_TO = 21;
        $DATESTAMP_1 = '20151123';
        $DATESTAMP_2 = '20151124';
        $CURRENT_BALANCES = [
            $ACC_FROM => [
                Balance::ATTR_BALANCE_CLOSE => 10
            ],
            $ACC_TO => [
                Balance::ATTR_BALANCE_CLOSE => 20
            ]
        ];
        $TRANS = [
            [
                Transaction::ATTR_DEBIT_ACC_ID => $ACC_FROM,
                Transaction::ATTR_CREDIT_ACC_ID => $ACC_TO,
                Transaction::ATTR_DATE_APPLIED => '2015-11-23 18:19:20',
                Transaction::ATTR_VALUE => '10.00',
            ],
            [
                Transaction::ATTR_DEBIT_ACC_ID => $ACC_FROM,
                Transaction::ATTR_CREDIT_ACC_ID => $ACC_TO,
                Transaction::ATTR_DATE_APPLIED => '2015-11-23 18:19:22',
                Transaction::ATTR_VALUE => '20.00',
            ],
            [
                Transaction::ATTR_DEBIT_ACC_ID => $ACC_FROM,
                Transaction::ATTR_CREDIT_ACC_ID => $ACC_TO,
                Transaction::ATTR_DATE_APPLIED => '2015-11-24 18:20:24',
                Transaction::ATTR_VALUE => '30.00',
            ]
        ];

        /** === Setup Mocks === */
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrentOld')->once()
            ->andReturn($DATESTAMP_1);
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrentOld')->once()
            ->andReturn($DATESTAMP_1);
        $this->mToolPeriod
            ->shouldReceive('getPeriodCurrentOld')->once()
            ->andReturn($DATESTAMP_2);
        /** === Call and asserts  === */
        $updates = $this->obj->calcBalances($CURRENT_BALANCES, $TRANS);
        $this->assertTrue(is_array($updates));
        $from = $updates[$ACC_FROM];
        $this->assertTrue(is_array($from));
        $data = $from[$DATESTAMP_1];
        $this->assertTrue(is_array($data));
    }
}