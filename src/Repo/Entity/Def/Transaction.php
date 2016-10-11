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

}