<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Accounting\Service\Account\Balance\Calc\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Calc\Response as AResponse;

/**
 * Calculate daily balances.
 *
 * This service is not used outside this module.
 */
class Calc
{
    private const DEF_DAYS_TO_RESET = 2;

    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTransaction;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;
    /** @var  \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Accounting\Service\Account\Balance\LastDate */
    private $servBalanceLastDate;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\Simple Simple balance calculator. */
    private $servCalcSimple;
    /** @var \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType */
    private $ownProcessOneType;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTransaction,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Service\Account\Balance\LastDate $servBalanceLastDate,
        \Praxigento\Accounting\Service\Account\Balance\Calc\Simple $servCalcSimple,
        \Praxigento\Accounting\Service\Account\Balance\Calc\A\ProcessOneType $ownProcessOneType
    ) {
        $this->logger = $logger;
        $this->daoBalance = $daoBalance;
        $this->daoTransaction = $daoTransaction;
        $this->daoTypeAsset = $daoTypeAsset;
        $this->hlpDate = $hlpDate;
        $this->hlpPeriod = $hlpPeriod;
        $this->servBalanceLastDate = $servBalanceLastDate;
        $this->servCalcSimple = $servCalcSimple;
        $this->ownProcessOneType = $ownProcessOneType;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        /** define local working data */
        $assetTypeId = $request->getAssetTypeId();
        $assetTypeCode = $request->getAssetTypeCode();
        $daysToReset = $request->getDaysToReset();

        /** validate pre-processing conditions */
        $dsBalanceClose = $this->getDateBalanceClose($daysToReset);
        $assets = $this->getAssetTypes($assetTypeId, $assetTypeCode);
        $total = count($assets);
        $this->logger->info("Total $total asset types will be re-calculated from '$dsBalanceClose'.");

        /** perform processing */
        foreach ($assets as $typeId => $typeCode) {
            $this->logger->info("Re-calc balances for asset $typeCode/$typeId.");
            $this->ownProcessOneType->exec($typeId, $dsBalanceClose);
        }
        $result->markSucceed();

//        /* get the last balance date */
//        $lastDate = $this->getBalancesLastDate($assetTypeId, $assetTypeCode);
//        $balances = $this->daoBalance->getOnDate($assetTypeId, $lastDate);
//        /* check date to */
//        if (is_null($dateTo)) {
//            /* use 'yesterday' */
//            $dtNow = $this->hlpDate->getUtcNow();
//            $today = $this->hlpPeriod->getPeriodCurrent($dtNow);
//            $dateTo = $this->hlpPeriod->getPeriodPrev($today);
//        }
//        /* get transactions for period */
//        if ($lastDate) {
//            /* first date should be after balance last date */
//            $dtFrom = $this->hlpPeriod->getTimestampNextFrom($lastDate);
//            $dtTo = $this->hlpPeriod->getTimestampTo($dateTo);
//            $trans = $this->daoTransaction->getForPeriod($assetTypeId, $dtFrom, $dtTo);
//            $updates = $this->servCalcSimple->exec($balances, $trans);
//            $this->daoBalance->updateBalances($updates);
//            $result->markSucceed();
//        }
        return $result;
    }

    /**
     * Validate given asset type or get all types.
     *
     * @param $assetTypeId
     * @param $assetTypeCode
     * @return array [id => code]
     */
    private function getAssetTypes($assetTypeId, $assetTypeCode)
    {
        $result = [];
        $types = $this->daoTypeAsset->get();
        if (
            is_null($assetTypeId) &&
            is_null($assetTypeCode)
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
                    ($typeId == $assetTypeId) ||
                    ($typeCode == $assetTypeCode)
                ) {
                    $result[$typeId] = $typeCode;
                    break;
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

    private function getAssetTypesIds()
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