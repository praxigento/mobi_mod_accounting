<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2017
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Balance as EBalance;
use Praxigento\Accounting\Service\Account\Balance\Reset\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Reset\Response as AResponse;

class Reset
{
    /** @var \Praxigento\Accounting\Repo\Dao\Balance */
    private $daoBalance;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Balance $daoBalance
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->daoBalance = $daoBalance;
    }

    /**
     * Reset balance history for all accounts on dates after requested (excl.).
     *
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $accounts = $request->getAccounts();
        $assetTypes = $request->getAssetTypes();
        $dateFrom = $request->getDateFrom();

        if (is_array($accounts)) {
            /* reset balances for the given accounts */
            $deleted = $this->resetByAccounts($accounts, $dateFrom);
        } elseif (is_array($assetTypes)) {
            /* reset balances for the given assets */
            $deleted = $this->resetByAssets($assetTypes, $dateFrom);
        } else {
            /* reset all balances */
            $deleted = $this->resetAll($dateFrom);
        }
        $this->logger->info("Total $deleted balances where reset.");
        /** compose result */
        $result = new AResponse();
        $result->setRowsDeleted($deleted);
        $result->markSucceed();
        return $result;
    }

    /**
     * Quotes string data to use inside SQL queries.
     *
     * @param string $data
     * @return string
     */
    private function quote($data)
    {
        $conn = $this->resource->getConnection();
        $result = $conn->quote($data);
        return $result;
    }

    /**
     * Reset all balances starting from date.
     *
     * @param $dateFrom
     * @return int
     */
    private function resetAll($dateFrom)
    {
        $quoted = $this->quote($dateFrom);
        $where = EBalance::A_DATE . '>' . $quoted;
        $result = (int)$this->daoBalance->delete($where);
        return $result;
    }

    private function resetByAccounts($accounts, $dateFrom)
    {
        $result = 0;
        $json = json_encode($accounts);
        $this->logger->info("Reset balances greater than '$dateFrom' for accounts: $json.");
        if (count($accounts)) {
            $quoted = $this->quote($dateFrom);
            $where = EBalance::A_ACCOUNT_ID . ' IN (';
            foreach ($accounts as $account) {
                $id = (int)$account;
                $where .= "$id,";
            }
            /* trim the last ',' */
            $where = substr($where, 0, strlen($where) - 1);
            $where .= ')';
            $where .= " AND (" . EBalance::A_DATE . ">$quoted)";
            $result = (int)$this->daoBalance->delete($where);
        }
        return $result;
    }

    private function resetByAssets($assets, $dateFrom)
    {
        $result = 0;
        $json = json_encode($assets);
        $this->logger->info("Reset balances greater than '$dateFrom' for assets: $json.");
        if (count($assets)) {
            $query = $this->queryDeleteByAssets($assets, $dateFrom);
            $conn = $this->resource->getConnection();
            $stmt = $conn->query($query);
            $result = $stmt->rowCount();
        }
        return $result;
    }

    /**
     * Compose query to delete balances filtered by asset types and date.
     *
     * @param int[] $assetTypeIds
     * @param string $dateFrom
     * @return string
     */
    private function queryDeleteByAssets($assetTypeIds, $dateFrom)
    {
        $tblAcc = $this->resource->getTableName(EAccount::ENTITY_NAME);
        $tblBal = $this->resource->getTableName(EBalance::ENTITY_NAME);
        $conn = $this->resource->getConnection();
        $quoted = $conn->quote($dateFrom);
        $in = '';
        foreach ($assetTypeIds as $id) {
            $in .= (int)$id . ',';
        }
        $in = substr($in, 0, strlen($in) - 1);
        $select = "SELECT " . EAccount::A_ID . " FROM $tblAcc";
        $select .= " WHERE " . EAccount::A_ASSET_TYPE_ID . " IN ($in)";
        $result = "DELETE FROM $tblBal WHERE " . EBalance::A_ACCOUNT_ID . " IN ($select)"
            . " AND (" . EBalance::A_DATE . ">$quoted)";
        return $result;
    }
}