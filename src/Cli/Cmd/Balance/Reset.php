<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Cli\Cmd\Balance;

/**
 * Reset accounts balances.
 */
class Reset
    extends \Praxigento\Core\Cli\Cmd\Base
{

    const OPT_DATESTAMP_NAME = 'date';
    const OPT_DATESTAMP_SHORTCUT = 'd';
    /** @var \Praxigento\Accounting\Service\IBalance */
    protected $callBalance;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Service\IBalance $callBalance
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:reset',
            'Reset accounts balances.'
        );
        $this->callBalance = $callBalance;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_DATESTAMP_NAME,
            self::OPT_DATESTAMP_SHORTCUT,
            \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
            'Date from (inclusive) to reset balances (-d 20170308).'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln("<info>Start reset of the accounts balances.<info>");
        /* get CLI input parameters */
        $dstamp = $input->getOption(self::OPT_DATESTAMP_NAME);

        /* perform action */
        $req = new \Praxigento\Accounting\Service\Balance\Request\Reset();
        $req->setDateFrom($dstamp);
        $this->callBalance->reset($req);

        $output->writeln('<info>Command is completed.<info>');

    }

}