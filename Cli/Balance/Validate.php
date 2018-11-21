<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Accounting\Cli\Balance;

/**
 * Validate & fix accounts balances.
 */
class Validate
    extends \Praxigento\Core\App\Cli\Cmd\Base
{

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:validate',
            'Validate & fix accounts balances.'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        /* get CLI input parameters */
        $output->writeln("<info>Command '" . $this->getName() . "':<info>");

        /* perform action */


        $output->writeln('<info>Command is completed.<info>');

    }

}