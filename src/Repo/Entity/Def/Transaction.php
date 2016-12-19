<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Def;

use Praxigento\Accounting\Data\Entity\Transaction as Entity;

class Transaction
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Accounting\Repo\Entity\ITransaction
{
    /** @var \Praxigento\Accounting\Repo\Entity\IAccount */
    protected $_repoAccount;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric,
        \Praxigento\Accounting\Repo\Entity\IAccount $repoAccount
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
        $this->_repoAccount = $repoAccount;
    }

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
        $paramAssetType = $this->_conn->quote($assetTypeId, \Zend_Db::INT_TYPE);
        $asAccount = 'acc';
        $asTrans = 'trn';
        $tblAccount = $this->_resource->getTableName(\Praxigento\Accounting\Data\Entity\Account::ENTITY_NAME);
        $tblTrans = $this->_resource->getTableName(\Praxigento\Accounting\Data\Entity\Transaction::ENTITY_NAME);
        /* select from prxgt_acc_account  */
        $query = $this->_conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join prxgt_acc_transaction  */
        $on = $asAccount . '.' . \Praxigento\Accounting\Data\Entity\Account::ATTR_ID . '='
            . $asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DEBIT_ACC_ID;
        $query->join([$asTrans => $tblTrans], $on);
        /* where */
        $query->where($asAccount . '.' . \Praxigento\Accounting\Data\Entity\Account::ATTR_ASSET_TYPE_ID . '=:asset_type_id');
        $query->where($asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_ID . ' IS NOT NULL');
        $query->where($asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DATE_APPLIED
            . '>=:date_from');
        $query->where($asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DATE_APPLIED
            . '<=:date_to');
        $bind = [
            'asset_type_id' => $paramAssetType,
            'date_from' => $timestampFrom,
            'date_to' => $timestampTo
        ];
        /* order by */
        $query->order($asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DATE_APPLIED . ' ASC');
        // $sql = (string)$query;
        $result = $this->_conn->fetchAll($query, $bind);
        return $result;
    }

    /**
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
        $tblAccount = $this->_resource->getTableName(\Praxigento\Accounting\Data\Entity\Account::ENTITY_NAME);
        $tblTrans = $this->_resource->getTableName(\Praxigento\Accounting\Data\Entity\Transaction::ENTITY_NAME);
        /* select from account */
        $query = $this->_conn->select();
        $query->from([$asAccount => $tblAccount], []);
        /* join transactions on debit account */
        $on = $asAccount . '.' . \Praxigento\Accounting\Data\Entity\Account::ATTR_ID . '='
            . $asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DEBIT_ACC_ID;
        $query->joinLeft(
            [$asTrans => $tblTrans],
            $on,
            [\Praxigento\Accounting\Data\Entity\Transaction::ATTR_DATE_APPLIED]
        );
        /* where */
        $query->where($asAccount . '.' . \Praxigento\Accounting\Data\Entity\Account::ATTR_ASSET_TYPE_ID . '=:typeId');
        $bind = ['typeId' => $assetTypeId];
        $query->where($asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DATE_APPLIED
            . ' IS NOT NULL');
        /* order by */
        $query->order([$asTrans . '.' . \Praxigento\Accounting\Data\Entity\Transaction::ATTR_DATE_APPLIED . ' ASC']);
        /* perform query */
        // $sql = (string)$query;
        $result = $this->_conn->fetchOne($query, $bind);
        return $result;
    }

}