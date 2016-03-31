<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Balance\Sub;

use Praxigento\Accounting\Lib\Entity\Balance;
use Praxigento\Accounting\Lib\Entity\Transaction;
use Praxigento\Accounting\Lib\Service\Balance\Sub;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class CalcSimple_UnitTest extends \Praxigento\Core\Lib\Test\BaseTestCase {

    public function test_calcBalances() {
        /** === Test Data === */
        $ACC_FROM = 12;
        $ACC_TO = 21;
        $DATESTAMP_1 = '20151123';
        $DATESTAMP_2 = '20151124';
        $CURRENT_BALANCES = [
            $ACC_FROM => [
                Balance::ATTR_BALANCE_CLOSE => 10
            ],
            $ACC_TO   => [
                Balance::ATTR_BALANCE_CLOSE => 20
            ]
        ];
        $TRANS = [
            [
                Transaction::ATTR_DEBIT_ACC_ID  => $ACC_FROM,
                Transaction::ATTR_CREDIT_ACC_ID => $ACC_TO,
                Transaction::ATTR_DATE_APPLIED  => '2015-11-23 18:19:20',
                Transaction::ATTR_VALUE         => '10.00',
            ], [
                Transaction::ATTR_DEBIT_ACC_ID  => $ACC_FROM,
                Transaction::ATTR_CREDIT_ACC_ID => $ACC_TO,
                Transaction::ATTR_DATE_APPLIED  => '2015-11-23 18:19:22',
                Transaction::ATTR_VALUE         => '20.00',
            ], [
                Transaction::ATTR_DEBIT_ACC_ID  => $ACC_FROM,
                Transaction::ATTR_CREDIT_ACC_ID => $ACC_TO,
                Transaction::ATTR_DATE_APPLIED  => '2015-11-24 18:20:24',
                Transaction::ATTR_VALUE         => '30.00',
            ]
        ];
        /** === Mocks === */
        $mToolPeriod = $this->_mockFor('Praxigento\Core\Lib\Tool\Period');

        // $date = $this->_toolPeriod->getPeriodCurrent($timestamp, Period::TYPE_DAY);
        $mToolPeriod
            ->expects($this->at(0))
            ->method('getPeriodCurrent')
            ->will($this->returnValue($DATESTAMP_1));
        $mToolPeriod
            ->expects($this->at(1))
            ->method('getPeriodCurrent')
            ->will($this->returnValue($DATESTAMP_1));
        $mToolPeriod
            ->expects($this->at(2))
            ->method('getPeriodCurrent')
            ->will($this->returnValue($DATESTAMP_2));
        /**
         * Prepare request and perform call.
         */
        /** @var  $sub CalcSimple */
        $sub = new CalcSimple($mToolPeriod);
        $updates = $sub->calcBalances($CURRENT_BALANCES, $TRANS);
        $this->assertTrue(is_array($updates));
        $from = $updates[$ACC_FROM];
        $this->assertTrue(is_array($from));
        $data = $from[$DATESTAMP_1];
        $this->assertTrue(is_array($data));
    }
}