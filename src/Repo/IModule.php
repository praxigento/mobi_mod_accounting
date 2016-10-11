<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo;

use Praxigento\Core\ICached;

interface IModule extends ICached
{

    /**
     * Get maximal datestamp for existing balance by asset type id or null if no data is found.
     *
     * @param int $assetTypeId
     *
     * @return string YYYYMMDD
     */
    public function getBalanceMaxDate($assetTypeId = null);

    /**
     *
     * @param $assetTypeId
     * @param $yyyymmdd
     *
     * @return array
     */
    public function getBalancesOnDate($assetTypeId, $yyyymmdd);

    /**
     * Return MageID for customer that represents store in accounting.
     * @return int
     */
    public function getRepresentativeCustomerId();

    /**
     * Return representative account ID for given asset type.
     *
     * @param int $assetTypeId
     * @return int|null
     */
    public function getRepresentativeAccountId($assetTypeId);

    /**
     * Get date for first transaction.
     *
     * @param null $assetTypeId
     *
     * @return mixed
     */
    public function getTransactionMinDateApplied($assetTypeId = null);

    /**
     * @param $assetTypeId
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return mixed
     */
    public function getTransactionsForPeriod($assetTypeId, $timestampFrom, $timestampTo);

    /**
     * @param $updateData
     * @return mixed
     */
    public function updateBalances($updateData);
}