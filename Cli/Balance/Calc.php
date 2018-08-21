<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Cli\Balance;

/**
 * Calculate accounts balances.
 */
class Calc
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_DATESTAMP_DEF = '31171231';
    const OPT_DATESTAMP_NAME = 'date';
    const OPT_DATESTAMP_SHORTCUT = 'd';
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    protected $daoTypeAsset;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc */
    protected $servBalance;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Accounting\Service\Account\Balance\Calc $servBalance
    ) {
        parent::__construct(
            $manObj,
            'prxgt:acc:balance:calc',
            'Calculate accounts balances.'
        );
        $this->daoTypeAsset = $daoTypeAsset;
        $this->servBalance = $servBalance;
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
        $output->writeln("<info>Start calculation of the accounts balances (up to $period).<info>");

        /* perform action */
        $assets = $this->getAssetTypesIds();
        foreach ($assets as $typeId => $typeCode) {
            $req = new \Praxigento\Accounting\Service\Account\Balance\Calc\Request();
            $req->setAssetTypeId($typeId);
            $req->setDateTo($period);
            $resp = $this->servBalance->exec($req);
            if ($resp->isSucceed()) {
                $output->writeln("<info>Balances for asset '$typeCode' are calculated.<info>");
            } else {
                $output->writeln("<info>Balances for asset '$typeCode' are NOT calculated.<info>");
            }
        }
        $output->writeln('<info>Command is completed.<info>');
    }

    /**
     * Get IDs for all asset types.
     * @return array
     */
    protected function getAssetTypesIds()
    {
        $result = [];
        $types = $this->daoTypeAsset->get();
        foreach ($types as $type) {
            /* convert to DataObject if repo response is array */
            /** @var \Praxigento\Accounting\Repo\Data\Type\Asset $obj */
            $obj = (is_array($type)) ? new \Praxigento\Accounting\Repo\Data\Type\Asset($type) : $type;
            $typeId = $obj->getId();
            $typeCode = $obj->getCode();
            $result[$typeId] = $typeCode;
        }
        return $result;
    }
}