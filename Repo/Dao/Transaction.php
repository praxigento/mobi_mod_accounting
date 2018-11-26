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

}