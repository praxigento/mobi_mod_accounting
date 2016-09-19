<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg;

use Praxigento\Accounting\Data\Agg\Account as AggAccount;

interface IAccount
    extends \Praxigento\Core\Repo\ICrud
{
    const AS_ACCOUNT = 'paa';
    const AS_TYPE_ASSET = 'pata';
    const AS_ATTR_NAME_FIRST = 'anf';
    const AS_ATTR_NAME_LAST = 'anl';

    /**
     * @param array|AggAccount $data
     * @return AggAccount
     */
    public function create($data);

    /**
     * @param int $id
     * @return AggAccount|null
     */
    public function getById($id);
}