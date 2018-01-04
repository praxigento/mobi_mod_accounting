<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Cli\Balance;

/**
 * Reset accounts balances.
 */
class Reset
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_DATESTAMP_DEF = '10170101';
    const OPT_DATESTAMP_NAME = 'date';
    const OPT_DATESTAMP_SHORTCUT = 'd';
    /** @var \Praxigento\Accounting\Service\Account\Balance\Reset */
    protected $balanceReset;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Service\Account\Balance\Reset $balanceReset
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:reset',
            'Reset accounts balances.'
        );
        $this->balanceReset = $balanceReset;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_DATESTAMP_NAME,
            self::OPT_DATESTAMP_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'Date from (inclusive) to reset balances (-d 20170308).',
            self::OPT_DATESTAMP_DEF
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* get CLI input parameters */
        $period = $input->getOption(self::OPT_DATESTAMP_NAME);
        $output->writeln("<info>Start reset of the accounts balances (from '$period').<info>");

        /* perform action */
        $req = new \Praxigento\Accounting\Service\Account\Balance\Reset\Request();
        $req->setDateFrom($period);
        $this->balanceReset->exec($req);

        $output->writeln('<info>Command is completed.<info>');

    }

}