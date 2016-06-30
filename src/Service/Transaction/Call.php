<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Transaction;

use Praxigento\Accounting\Data\Entity\Transaction as Transaction;
use Praxigento\Accounting\Service\ITransaction;

class Call extends \Praxigento\Core\Service\Base\Call implements ITransaction
{
    /** @var  \Praxigento\Core\Repo\Transaction\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Accounting\Repo\Entity\IAccount */
    protected $_repoAcc;
    /** @var  \Praxigento\Accounting\Repo\Entity\ITransaction */
    protected $_repoTrans;

    /**
     * Call constructor.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Repo\Transaction\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\IAccount $repoAcc,
        \Praxigento\Accounting\Repo\Entity\ITransaction $repoTrans
    ) {
        parent::__construct($logger);
        $this->_manTrans = $manTrans;
        $this->_repoAcc = $repoAcc;
        $this->_repoTrans = $repoTrans;
    }

    /**
     * Add new transaction and update current balances.
     *
     * @param Request\Add $request
     *
     * @return Response\Add
     */
    public function add(Request\Add $request)
    {
        $result = new Response\Add();
        $debitAccId = $request->getDebitAccId();
        $creditAccId = $request->getCreditAccId();
        $operationId = $request->getOperationId();
        $dateApplied = $request->getDateApplied();
        $value = $request->getValue();
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* get account type for debit account */
            $debitAcc = $this->_repoAcc->getById($debitAccId);
            $debitAssetTypeId = $debitAcc->getAssetTypeId();
            /* get account type for credit account */
            $creditAcc = $this->_repoAcc->getById($creditAccId);
            $creditAssetTypeId = $creditAcc->getAssetTypeId();
            /* asset types should be equals */
            if (
                !is_null($debitAssetTypeId) &&
                ($debitAssetTypeId == $creditAssetTypeId)
            ) {
                /* add transaction */
                $toAdd = [
                    Transaction::ATTR_OPERATION_ID => $operationId,
                    Transaction::ATTR_DEBIT_ACC_ID => $debitAccId,
                    Transaction::ATTR_CREDIT_ACC_ID => $creditAccId,
                    Transaction::ATTR_VALUE => $value,
                    Transaction::ATTR_DATE_APPLIED => $dateApplied
                ];
                $idCreated = $this->_repoTrans->create($toAdd);
                if ($idCreated) {
                    /* update debit balance */
                    $this->_repoAcc->updateBalance($debitAccId, 0 - $value);
                    /* update credit balance */
                    $this->_repoAcc->updateBalance($creditAccId, 0 + $value);
                    $result->setTransactionId($idCreated);
                }
            } else {
                throw new \Exception("Asset type (#$debitAssetTypeId) for debit account #$debitAccId is not equal to "
                    . "asset type (#$creditAssetTypeId) for credit account $creditAccId.");
            }
            $this->_manTrans->transactionCommit($trans);
            $result->markSucceed();
        } finally {
            $this->_manTrans->transactionClose($trans);

        }
        return $result;
    }

}