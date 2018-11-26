<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao;

use Praxigento\Accounting\Repo\Data\Transaction as Entity;

class Transaction
    extends \Praxigento\Core\App\Repo\Dao
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $repoAccount;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount
    )
    {
        parent::__construct($resource, $daoGeneric, Entity::class);
        $this->repoAccount = $daoAccount;
    }

    /**
     * @param null $where
     * @param null $order
     * @param null $limit
     * @param null $offset
     * @param null $columns
     * @param null $group
     * @param null $having
     * @return Entity[]
     */
    public function get($where = null,
                        $order = null,
                        $limit = null,
                        $offset = null,
                        $columns = null,
                        $group = null,
                        $having = null)
    {
        return parent::get($where, $order, $limit, $offset, $columns, $group, $having);
    }

    /**
     * Create transaction and update balances in account table.
     *
     * @param \Praxigento\Accounting\Repo\Data\Transaction|array $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        if ($result) {
            /* update balances for accounts */
            if (is_array($data)) {
                $data = new Entity($data);
            }
            $value = $data->getValue();
            $creditAccId = $data->getCreditAccId();
            $debitAccId = $data->getDebitAccId();
            $this->repoAccount->updateBalance($creditAccId, 0 + $value);
            $this->repoAccount->updateBalance($debitAccId, 0 - $value);
        }
        return $result;
    }

    /**
     * @param $assetTypeId
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return mixed
     *
     * SELECT
     * `trn`.*
     * FROM `prxgt_acc_account` AS `acc`
     * INNER JOIN `prxgt_acc_transaction` AS `trn`
     * ON acc.id = trn.debit_acc_id
     * WHERE (acc.asset_type_id = :asset_type_id)
     * AND (trn.id IS NOT NULL)
     * AND (trn.date_applied >= :date_from)
     * AND (trn.date_applied <= :date_to)
     * ORDER BY `trn`.`date_applied` ASC
     *
     * @param $assetTypeId
     * @param $timestampFrom
     * @param $timestampTo
     *
     * @return array
     */
    public function getForPeriod($assetTypeId, $timestampFrom, $timestampTo)
    {
        $paramAssetType = $this->conn->quote($assetTypeId, \Zend_Db::INT_TYPE);
        $asAccount = 'acc';
        $asTrans = 'trn';
        $tblAccount = $this->resource->getTableName(\Praxigento\Accounting\Repo\Data\Account::ENTITY_NAME);
        $tblTrans = $this->resource->getTableName(\Praxigento\Accounting\Repo\Data\Transaction::ENTITY_NAME);
        /* select from prxgt_acc_account  */
        $query = $this->conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join prxgt_acc_transaction  */
        $on = $asAccount . '.' . \Praxigento\Accounting\Repo\Data\Account::A_ID . '='
            . $asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DEBIT_ACC_ID;
        $query->join([$asTrans => $tblTrans], $on);
        /* where */
        $query->where($asAccount . '.' . \Praxigento\Accounting\Repo\Data\Account::A_ASSET_TYPE_ID . '=:asset_type_id');
        $query->where($asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_ID . ' IS NOT NULL');
        $query->where($asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DATE_APPLIED
            . '>=:date_from');
        $query->where($asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DATE_APPLIED
            . '<:date_to');
        $bind = [
            'asset_type_id' => $paramAssetType,
            'date_from' => $timestampFrom,
            'date_to' => $timestampTo
        ];
        /* order by */
        $query->order($asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DATE_APPLIED . ' ASC');
        $result = $this->conn->fetchAll($query, $bind);
        return $result;
    }

}