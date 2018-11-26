<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2017
 */

namespace Praxigento\Accounting\Service\Account\Balance;

use Praxigento\Accounting\Config as Cfg;
use Praxigento\Accounting\Service\Account\Balance\Change\Request as ARequest;
use Praxigento\Accounting\Service\Account\Balance\Change\Response as AResponse;

class Change
{
    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAccount;
    /** @var \Praxigento\Accounting\Repo\Dao\Log\Change\Admin */
    private $daoLogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Dao\Operation */
    private $daoOperation;
    /** @var \Praxigento\Accounting\Repo\Dao\Transaction */
    private $daoTransaction;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Operation */
    private $daoTypeOper;
    /** @var \Praxigento\Core\Api\Helper\Date */
    private $hlpDate;

    public function __construct(
        \Praxigento\Core\Api\Helper\Date $hlpDate,
        \Praxigento\Accounting\Repo\Dao\Account $daoAccount,
        \Praxigento\Accounting\Repo\Dao\Operation $daoOperation,
        \Praxigento\Accounting\Repo\Dao\Transaction $daoTransaction,
        \Praxigento\Accounting\Repo\Dao\Type\Operation $daoTypeOper,
        \Praxigento\Accounting\Repo\Dao\Log\Change\Admin $daoLogChangeAdmin
    ) {
        $this->hlpDate = $hlpDate;
        $this->daoAccount = $daoAccount;
        $this->daoOperation = $daoOperation;
        $this->daoTransaction = $daoTransaction;
        $this->daoTypeOper = $daoTypeOper;
        $this->daoLogChangeAdmin = $daoLogChangeAdmin;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $result = new AResponse();
        $accCustId = $request->getCustomerAccountId();
        $adminUserId = $request->getAdminUserId();
        $value = $request->getChangeValue();
        /* get account's asset type by ID */
        $assetTypeId = $this->daoAccount->getAssetTypeId($accCustId);
        /* get system account id for given asset type */
        $accSysId = $this->daoAccount->getSystemAccountId($assetTypeId);
        /* get operation type by code and date performed */
        $operTypeId = $this->daoTypeOper->getIdByCode(Cfg::CODE_TYPE_OPER_CHANGE_BALANCE);
        $dateNow = $this->hlpDate->getUtcNowForDb();
        /* create operation */
        $operation = new \Praxigento\Accounting\Repo\Data\Operation();
        $operation->setTypeId($operTypeId);
        $operation->setDatePerformed($dateNow);
        $operId = $this->daoOperation->create($operation);
        /* create transaction */
        $trans = new \Praxigento\Accounting\Repo\Data\Transaction();
        $trans->setOperationId($operId);
        $trans->setDateApplied($dateNow);
        if ($value > 0) {
            $trans->setDebitAccId($accSysId);
            $trans->setCreditAccId($accCustId);
        } else {
            $trans->setDebitAccId($accCustId);
            $trans->setCreditAccId($accSysId);
        }
        $trans->setValue(abs($value));
        $this->daoTransaction->create($trans);
        /* log details (operator name who performs the operation) */
        $log = new \Praxigento\Accounting\Repo\Data\Log\Change\Admin();
        $log->setOperationRef($operId);
        $log->setUserRef($adminUserId);
        $this->daoLogChangeAdmin->create($log);
        $result->markSucceed();
        return $result;
    }
}