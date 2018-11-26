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
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Api\Service\Account\Balance\Calc $servBalance
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:calc',
            'Re-calculate accounts balances (reset daily balances up to $days).'
        );
        $this->logger = $logger;
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
        $msg = "Command '" . $this->getName() . "' (days to reset: $days):";
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);

        /* wrap all DB operations with DB transaction */
        $def = $this->manTrans->begin();
        $req = new \Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request();
        $req->setDaysToReset($days);
        $this->servBalance->exec($req);
        $this->manTrans->commit($def);

        $msg = "Command '" . $this->getName() . "' is completed.";
        $output->writeln("<info>$msg<info>");
        $this->logger->info($msg);
    }
}