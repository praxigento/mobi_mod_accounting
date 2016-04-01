<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Lib\Repo;

interface IModule
{
    /**
     * Reset cached data.
     */
    public function cacheReset();

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
     * @param string $code
     *
     * @return int
     */
    public function getTypeAssetIdByCode($code);

    /**
     * @param $code
     *
     * @return int
     */
    public function getTypeOperationIdByCode($code);

    /**
     * @param $updateData
     * @return mixed
     */
    public function updateBalances($updateData);
}