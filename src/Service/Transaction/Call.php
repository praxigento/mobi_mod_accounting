<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Transaction;

use Praxigento\Accounting\Repo\Entity\Data\Transaction as Transaction;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Accounting\Service\ITransaction
{
    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Accounting\Repo\Entity\Account */
    protected $_repoAcc;
    /** @var  \Praxigento\Accounting\Repo\Entity\Transaction */
    protected $_repoTrans;

    /**
     * Call constructor.
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Repo\Entity\Transaction $repoTrans
    ) {
        parent::__construct($logger, $manObj);
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
                    Transaction::ATTR_OPERATION_ID => $operationId,
                    Transaction::ATTR_DEBIT_ACC_ID => $debitAccId,
                    Transaction::ATTR_CREDIT_ACC_ID => $creditAccId,
                    Transaction::ATTR_VALUE => $value,
                    Transaction::ATTR_DATE_APPLIED => $dateApplied
                ];
                if (!is_null($note)) {
                    $toAdd[Transaction::ATTR_NOTE] = $note;
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