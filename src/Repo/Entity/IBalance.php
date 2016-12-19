<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Entity;

use Praxigento\Accounting\Data\Entity\Balance as EntityData;
use Praxigento\Core\Repo\ICrud;

interface IBalance extends ICrud
{
    /**
     * @param array|EntityData $data
     * @return int
     */
    public function create($data);

    /**
     * @param int $id
     * @return EntityData|bool
     */
    public function getById($id);

    /**
     * Get maximal datestamp for existing balance by asset type id or null if no data is found.
     *
     * @param int $assetTypeId
     *
     * @return string YYYYMMDD
     */
    public function getMaxDate($assetTypeId = null);

    /**
     * Get balances on concrete date.
     *
     * @param $assetTypeId
     * @param $yyyymmdd
     *
     * @return array
     */
    public function getOnDate($assetTypeId, $yyyymmdd);


    /**
     * @param $updateData
     * @return mixed
     */
    public function updateBalances($updateData);
}