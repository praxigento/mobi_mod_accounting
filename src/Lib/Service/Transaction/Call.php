<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Lib\Service\Transaction;

use Praxigento\Accounting\Data\Entity\Account as Account;
use Praxigento\Accounting\Data\Entity\Transaction as Transaction;
use Praxigento\Accounting\Lib\Service\Account\Request\UpdateBalance as UpdateBalanceRequest;
use Praxigento\Accounting\Lib\Service\ITransaction;
use Praxigento\Core\Lib\Service\Repo\Request\AddEntity as AddEntityRequest;
use Praxigento\Core\Lib\Service\Repo\Request\GetEntityByPk as GetEntityByPkRequest;

class Call extends \Praxigento\Core\Lib\Service\Base\Call implements ITransaction {
    /**
     * @var \Praxigento\Accounting\Lib\Service\Account\Call
     */
    protected $_callAccount;

    /**
     * Call constructor.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Lib\Context\IDbAdapter $dba,
        \Praxigento\Core\Lib\IToolbox $toolbox,
        \Praxigento\Core\Lib\Service\IRepo $callRepo,
        \Praxigento\Accounting\Lib\Service\Account\Call $callAccount
    ) {
        parent::__construct($logger, $dba, $toolbox, $callRepo);
        $this->_callAccount = $callAccount;
    }

    /**
     * Add new transaction and update current balances.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     */
    public function add(Request\Add $request) {
        $result = new Response\Add();
        $debitAccId = $request->getDebitAccId();
        $creditAccId = $request->getCreditAccId();
        $operationId = $request->getOperationId();
        $dateApplied = $request->getDateApplied();
        $value = $request->getValue();
        $this->_getConn()->beginTransaction();
        try {
            /* get account type for debit account */
            $reqByPk = new  GetEntityByPkRequest(Account::ENTITY_NAME, [ Account::ATTR_ID => $debitAccId ]);
            $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
            $debitAccId = $respByPk->getData(Account::ATTR_ID);
            $debitAssetTypeId = $respByPk->getData(Account::ATTR_ASSET_TYPE_ID);
            /* get account type for credit account */
            $reqByPk = new  GetEntityByPkRequest(Account::ENTITY_NAME, [ Account::ATTR_ID => $creditAccId ]);
            $respByPk = $this->_callRepo->getEntityByPk($reqByPk);
            $creditAccId = $respByPk->getData(Account::ATTR_ID);
            $creditAssetTypeId = $respByPk->getData(Account::ATTR_ASSET_TYPE_ID);
            /* asset types should be equals */
            if(
                !is_null($debitAssetTypeId) &&
                ($debitAssetTypeId == $creditAssetTypeId)
            ) {
                /* add transaction */
                $toAdd = [
                    Transaction::ATTR_OPERATION_ID  => $operationId,
                    Transaction::ATTR_DEBIT_ACC_ID  => $debitAccId,
                    Transaction::ATTR_CREDIT_ACC_ID => $creditAccId,
                    Transaction::ATTR_VALUE         => $value,
                    Transaction::ATTR_DATE_APPLIED  => $dateApplied
                ];
                $reqAdd = new  AddEntityRequest(Transaction::ENTITY_NAME, $toAdd);
                $respAdd = $this->_callRepo->addEntity($reqAdd);
                if($respAdd->isSucceed()) {
                    $reqUpdate = new UpdateBalanceRequest();
                    /* update debit balance */
                    $reqUpdate->setData(UpdateBalanceRequest::ACCOUNT_ID, $debitAccId);
                    $reqUpdate->setData(UpdateBalanceRequest::CHANGE_VALUE, 0 - $value);
                    $this->_callAccount->updateBalance($reqUpdate);
                    /* update credit balance */
                    $reqUpdate->setData(UpdateBalanceRequest::ACCOUNT_ID, $creditAccId);
                    $reqUpdate->setData(UpdateBalanceRequest::CHANGE_VALUE, 0 + $value);
                    $this->_callAccount->updateBalance($reqUpdate);
                    $tranId = $respAdd->getIdInserted();
                    $result->setTransactionId($tranId);
                }
            } else {
                throw new \Exception("Asset type (#$debitAssetTypeId) for debit account #$debitAccId is not equal to "
                                     . "asset type (#$creditAssetTypeId) for credit account $creditAccId.");
            }
            $this->_getConn()->commit();
            $result->setAsSucceed();
        } catch(\Exception $e) {
            $this->_getConn()->rollBack();
            throw $e;
        }
        return $result;
    }

}