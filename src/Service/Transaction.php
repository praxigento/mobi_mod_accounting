<?php
/**
 * User: makhovdmitrii@inbox.ru
 */

namespace Praxigento\Accounting\Service;

use Praxigento\Accounting\Repo\Entity\Data\Transaction as ATransaction;
use Praxigento\Accounting\Service\Transaction\Request as ARequest;
use Praxigento\Accounting\Service\Transaction\Response as AResponse;


class Transaction
{

    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Accounting\Repo\Entity\Account */
    protected $_repoAcc;
    /** @var  \Praxigento\Accounting\Repo\Entity\Transaction */
    protected $_repoTrans;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTrans
    )
    {
        $this->_manTrans = $manTrans;
        $this->_repoAcc = $repoAcc;
        $this->_repoTrans = $repoTrans;
    }

    /**
     * Add new transaction and update current balances.
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        $result = new AResponse();
        $debitAccId = $request->getDebitAccId();
        $creditAccId = $request->getCreditAccId();
        $operationId = $request->getOperationId();
        $dateApplied = $request->getDateApplied();
        $value = $request->getValue();
        $note = $request->getNote();
        $def = $this->_manTrans->begin();
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
                    ATransaction::ATTR_OPERATION_ID => $operationId,
                    ATransaction::ATTR_DEBIT_ACC_ID => $debitAccId,
                    ATransaction::ATTR_CREDIT_ACC_ID => $creditAccId,
                    ATransaction::ATTR_VALUE => $value,
                    ATransaction::ATTR_DATE_APPLIED => $dateApplied
                ];
                if (!is_null($note)) {
                    $toAdd[ATransaction::ATTR_NOTE] = $note;
                }
                $idCreated = $this->_repoTrans->create($toAdd);
                $result->setTransactionId($idCreated);
            } else {
                throw new \Exception("Asset type (#$debitAssetTypeId) for debit account #$debitAccId is not equal to "
                    . "asset type (#$creditAssetTypeId) for credit account $creditAccId.");
            }
            $this->_manTrans->commit($def);
            $result->markSucceed();
        } finally {
            $this->_manTrans->end($def);

        }
        return $result;
    }

}