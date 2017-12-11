<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Operation;

use Praxigento\Accounting\Repo\Entity\Data\Operation as EntityOperation;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Call
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Accounting\Service\IOperation
{
    /** @var  \Praxigento\Core\App\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Admin */
    protected $_repoELogChangeAdmin;
    /** @var \Praxigento\Accounting\Repo\Entity\Log\Change\Customer */
    protected $_repoELogChangeCust;
    /** @var  \Praxigento\Accounting\Repo\Entity\Operation */
    protected $_repoOper;
    /** @var  \Praxigento\Accounting\Repo\Entity\Type\Operation */
    protected $_repoTypeOper;
    /** @var Sub\Add */
    protected $_subAdd;

    public function __construct(
        \Praxigento\Core\App\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\App\Transaction\Database\IManager $manTrans,
        \Praxigento\Accounting\Repo\Entity\Operation $repoOper,
        \Praxigento\Accounting\Repo\Entity\Type\Operation $repoTypeOper,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Admin $repoELogChangeAdmin,
        \Praxigento\Accounting\Repo\Entity\Log\Change\Customer $repoELogChangeCust,
        Sub\Add $subAdd
    ) {
        parent::__construct($logger, $manObj);
        $this->_manTrans = $manTrans;
        $this->_repoTypeOper = $repoTypeOper;
        $this->_repoOper = $repoOper;
        $this->_repoELogChangeAdmin = $repoELogChangeAdmin;
        $this->_repoELogChangeCust = $repoELogChangeCust;
        $this->_subAdd = $subAdd;
    }

    /**
     * Add operation with list of transactions and change account balances.
     *
     * @param Request\Add $req
     *
     * @return Response\Add
     */
    public function add(Request\Add $req)
    {
        $result = new Response\Add();
        $operationTypeId = $req->getOperationTypeId();
        $operationTypeCode = $req->getOperationTypeCode();
        $datePerformed = $req->getDatePerformed();
        $note = $req->getOperationNote();
        $transactions = $req->getTransactions();
        $asRef = $req->getAsTransRef();
        $customerId = $req->getCustomerId();
        $adminUserId = $req->getAdminUserId();
        $def = $this->_manTrans->begin();
        try {
            /* add operation itself */
            if (!$operationTypeId) {
                $operationTypeId = $this->_repoTypeOper->getIdByCode($operationTypeCode);
            }
            $bindToAdd = [
                EntityOperation::ATTR_TYPE_ID => $operationTypeId,
                EntityOperation::ATTR_DATE_PREFORMED => $datePerformed
            ];
            if (!is_null($note)) {
                $bindToAdd[EntityOperation::ATTR_NOTE] = $note;
            }
            $operId = $this->_repoOper->create($bindToAdd);
            if ($operId) {
                $transIds = $this->_subAdd->transactions($operId, $transactions, $datePerformed, $asRef);
                $result->setOperationId($operId);
                $result->setTransactionsIds($transIds);
                /* log customer link */
                if ($customerId) {
                    $log = new \Praxigento\Accounting\Repo\Entity\Data\Log\Change\Customer();
                    $log->setCustomerRef($customerId);
                    $log->setOperationRef($operId);
                    $this->_repoELogChangeCust->create($log);
                }
                /* log admin link */
                if ($adminUserId) {
                    $log = new \Praxigento\Accounting\Repo\Entity\Data\Log\Change\Admin();
                    $log->setUserRef($adminUserId);
                    $log->setOperationRef($operId);
                    $this->_repoELogChangeAdmin->create($log);
                }
                $this->_manTrans->commit($def);
                $result->markSucceed();
            }
        } finally {
            $this->_manTrans->end($def);
        }
        return $result;
    }
}