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

    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Api\Service\Account\Balance\Calc */
    private $servBalance;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Api\Service\Account\Balance\Calc $servBalance
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:calc',
            'Re-calculate accounts balances (reset daily balances up to $days).'
        );
        $this->manTrans = $manTrans;
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

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* get CLI input parameters */
        $days = $input->getOption(self::OPT_DAYS_NAME);
        $output->writeln("<info>Command '" . $this->getName() . "' (days to reset: $days):<info>");

        /* wrap all DB operations with DB transaction */
        $def = $this->manTrans->begin();
        $req = new \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request();
        $req->setDaysToReset($days);
        $this->servBalance->exec($req);
        $this->manTrans->commit($def);

        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }
}