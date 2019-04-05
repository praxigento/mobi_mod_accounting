<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Cli\Balance;

/**
 * Re-calculate accounts balances.
 */
class Calc
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_DAYS_DEF = '2';
    const OPT_DAYS_NAME = 'days';
    const OPT_DAYS_SHORTCUT = 'd';

    /** @var \Praxigento\Accounting\Api\Service\Account\Balance\Calc */
    private $servBalance;

    public function __construct(
        \Praxigento\Accounting\Api\Service\Account\Balance\Calc $servBalance
    ) {
        parent::__construct(
            'prxgt:acc:balance:calc',
            'Re-calculate accounts balances (reset daily balances up to $days).'
        );
        $this->servBalance = $servBalance;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_DAYS_NAME,
            self::OPT_DAYS_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'Days to re-calc balances ("-d 7" - reset balances for the last week).',
            self::OPT_DAYS_DEF
        );

    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $days = $input->getOption(self::OPT_DAYS_NAME);
        $this->logInfo("Days to reset: $days.");
        $req = new \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request();
        $req->setDaysToReset($days);
        $this->servBalance->exec($req);
    }
}