<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Repo\Dao;

class Balance
    extends \Praxigento\Core\App\Repo\Dao
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric
    )
    {
        parent::__construct($resource, $daoGeneric, \Praxigento\Accounting\Repo\Data\Balance::class);
    }

    /**
     * @param array|\Praxigento\Accounting\Repo\Data\Balance $data
     * @return int
     */
    public function create($data)
    {
        $result = parent::create($data);
        return $result;
    }

    /**
     * @param int $id
     * @return \Praxigento\Accounting\Repo\Data\Balance|bool
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

}