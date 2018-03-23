<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao;

use Praxigento\Accounting\Repo\Data\Transaction as Entity;

class Transaction
    extends \Praxigento\Core\App\Repo\Def\Entity
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    protected $_repoAccount;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric,
        \Praxigento\Accounting\Repo\Dao\Account $repoAccount
    )
    {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->_repoAccount = $repoAccount;
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
            /* update balalnces for accounts */
            if (is_array($data)) {
                $data = new Entity($data);
            }
            $value = $data->getValue();
            $creditAccid = $data->getCreditAccId();
            $debitAccId = $data->getDebitAccId();
            $this->_repoAccount->updateBalance($creditAccid, 0 + $value);
            $this->_repoAccount->updateBalance($debitAccId, 0 - $value);
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
        // $sql = (string)$query;
        $result = $this->conn->fetchAll($query, $bind);
        return $result;
    }

    /**
     * Get date for first transaction.
     *
     * @param null $assetTypeId
     *
     * @return mixed
     *
     * SELECT pat.date_applied
     * FROM prxgt_acc_type_asset pata
     * LEFT JOIN prxgt_acc_account paa
     * ON pata.id = paa.asset_type_id
     * LEFT JOIN prxgt_acc_transaction pat
     * ON paa.id = pat.debit_acc_id
     * WHERE pata.id = :assetTypeId
     * ORDER BY pat.date_applied ASC
     *
     * @param null $assetTypeId
     *
     * @return string
     */
    public function getMinDateApplied($assetTypeId = null)
    {
        $asAccount = 'a';
        $asTrans = 'trn';
        $tblAccount = $this->resource->getTableName(\Praxigento\Accounting\Repo\Data\Account::ENTITY_NAME);
        $tblTrans = $this->resource->getTableName(\Praxigento\Accounting\Repo\Data\Transaction::ENTITY_NAME);
        /* select from account */
        $query = $this->conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join transactions on debit account */
        $on = $asAccount . '.' . \Praxigento\Accounting\Repo\Data\Account::A_ID . '='
            . $asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DEBIT_ACC_ID;
        $query->joinLeft(
            [$asTrans => $tblTrans],
            $on,
            [\Praxigento\Accounting\Repo\Data\Transaction::A_DATE_APPLIED]
        );
        /* where */
        $query->where($asAccount . '.' . \Praxigento\Accounting\Repo\Data\Account::A_ASSET_TYPE_ID . '=:typeId');
        $bind = ['typeId' => $assetTypeId];
        $query->where($asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DATE_APPLIED
            . ' IS NOT NULL');
        /* order by */
        $query->order([$asTrans . '.' . \Praxigento\Accounting\Repo\Data\Transaction::A_DATE_APPLIED . ' ASC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->conn->fetchOne($query, $bind);
        return $result;
    }

}