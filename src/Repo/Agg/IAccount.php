<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg;

use Praxigento\Accounting\Data\Agg\Account as Agg;

interface IAccount
    extends \Praxigento\Core\Repo\ICrud
{
    const AS_ACCOUNT = 'paa';
    const AS_CUSTOMER = 'ce';
    const AS_TYPE_ASSET = 'pata';

    /**
     * @param array|Agg $data
     * @return Agg
     */
    public function create($data);

    /**
     * @param int $id
     * @return Agg|null
     */
    public function getById($id);
}