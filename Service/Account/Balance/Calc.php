<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2017
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Api\Service\Account\Balance\Calc\Request as ARequest;
use Praxigento\Accounting\Api\Service\Account\Balance\Calc\Response as AResponse;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;

/**
 * Re-calculate daily balances.
 */
class Calc
    implements \Praxigento\Accounting\Api\Service\Account\Balance\Calc
{
    private const DEF_DAYS_TO_RESET = 2;

    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneAccount */
    private $ownProcessOneAcc;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType */
    private $ownProcessOneType;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneAccount $ownProcessOneAcc,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType $ownProcessOneType
    ) {
        $this->logger = $logger;
        $this->daoTypeAsset = $daoTypeAsset;
        $this->hlpDate = $hlpDate;
        $this->hlpPeriod = $hlpPeriod;
        $this->ownProcessOneAcc = $ownProcessOneAcc;
        $this->ownProcessOneType = $ownProcessOneType;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        /** define local working data */
        $accountsIds = $request->getAccountsIds();
        $assetTypeCodes = $request->getAssetTypeCodes();
        $assetTypeIds = $request->getAssetTypeIds();
        $daysToReset = (int)$request->getDaysToReset();

        /** validate pre-processing conditions */
        $dsBalanceClose = $this->getDateBalanceClose($daysToReset);

        /** perform processing */
        if (
            is_array($accountsIds) &&
            (count($accountsIds) > 0)
        ) {
            $total = count($accountsIds);
            $this->logger->info("Total $total accounts will be re-calculated from '$dsBalanceClose'.");
            foreach ($accountsIds as $one) {
                $accId = (int)$one;
                $this->logger->info("Re-calc balances for account #$accId.");
                $this->ownProcessOneAcc->exec($accId, $dsBalanceClose);
            }
        } else {
            $assets = $this->getAssetTypes($assetTypeIds, $assetTypeCodes);
            $total = count($assets);
            $this->logger->info("Total $total asset types will be re-calculated from '$dsBalanceClose'.");
            foreach ($assets as $typeId => $typeCode) {
                $this->logger->info("Re-calc balances for asset $typeCode/$typeId.");
                $this->ownProcessOneType->exec($typeId, $dsBalanceClose);
            }
        }

        $result->markSucceed();
        return $result;
    }

    /**
     * Validate given asset type or get all types.
     *
     * @param int[] $typeIds
     * @param string[] $typeCodes
     * @return array [id => code]
     */
    private function getAssetTypes($typeIds, $typeCodes)
    {
        $result = [];
        $types = $this->daoTypeAsset->get();
        if (
            empty($typeIds) &&
            empty($typeCodes)
        ) {
            /* return all asset types */
            /** @var ETypeAsset $type */
            foreach ($types as $type) {
                $typeId = $type->getId();
                $typeCode = $type->getCode();
                $result[$typeId] = $typeCode;
            }
        } else {
            /* validate ID for given type ID or convert type code to type ID */
            /** @var ETypeAsset $type */
            foreach ($types as $type) {
                $typeId = $type->getId();
                $typeCode = $type->getCode();
                if (
                    (is_array($typeIds) && in_array($typeId, $typeIds)) ||
                    (is_array($typeCodes) && in_array($typeCode, $typeCodes))
                ) {
                    $result[$typeId] = $typeCode;
                }
            }
        }
        return $result;
    }

    /**
     * Calculate datestamp for the last day of balances to leave w/o reset.
     *
     * @param $daysToReset
     * @return string
     */
    private function getDateBalanceClose($daysToReset)
    {
        $days = abs((int)$daysToReset);
        if ($days < self::DEF_DAYS_TO_RESET) {
            $days = self::DEF_DAYS_TO_RESET;
        }
        $dtNow = $this->hlpDate->getUtcNow();
        $dtMod = $dtNow->modify("-$days days");
        $result = $this->hlpPeriod->getPeriodCurrent($dtMod);
        return $result;
    }

}