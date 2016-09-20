<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg;

use Praxigento\Accounting\Data\Agg\Operation as Agg;

interface IOperation
    extends \Praxigento\Core\Repo\ICrud
{
    const AS_OPER = 'pao';
    const AS_TYPE = 'pato';

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