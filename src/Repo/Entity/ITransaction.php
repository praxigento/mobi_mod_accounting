<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity;

interface ITransaction
    extends \Praxigento\Core\Repo\ICrud
{
    /**
     * Create transaction and update balances in account table.
     *
     * @param Praxigento\Accounting\Data\Entity\Transaction|array $data
     * @return int
     */
    public function create($data);

    /**
     * @param $assetTypeId
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return mixed
     */
    public function getForPeriod($assetTypeId, $timestampFrom, $timestampTo);

    /**
     * Get date for first transaction.
     *
     * @param null $assetTypeId
     *
     * @return mixed
     */
    public function getMinDateApplied($assetTypeId = null);
}