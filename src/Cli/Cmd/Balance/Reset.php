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


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance_reset',
            'Reset accounts balances.'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln("<info>Start reset of the accounts balances.<info>");

        $output->writeln('<info>Command is completed.<info>');

    }

}