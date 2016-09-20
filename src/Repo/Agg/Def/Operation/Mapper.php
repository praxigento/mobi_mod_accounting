<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Operation;

use Praxigento\Accounting\Data\Agg\Operation as Agg;
use Praxigento\Accounting\Data\Entity\Operation;
use Praxigento\Accounting\Data\Entity\Type\Operation as TypeOper;
use Praxigento\Accounting\Repo\Agg\IOperation as Repo;

class Mapper
    extends \Praxigento\Core\Repo\Query\Criteria\Def\Mapper
{
    public function __construct()
    {
        $this->_map = [
            Agg::AS_DATE_PERFORMED => Repo::AS_OPER . '.' . Operation::ATTR_DATE_PREFORMED,
            Agg::AS_ID => Repo::AS_OPER . '.' . Operation::ATTR_ID,
            Agg::AS_NOTE => Repo::AS_OPER . '.' . Operation::ATTR_NOTE,
            Agg::AS_TYPE => Repo::AS_TYPE . '.' . TypeOper::ATTR_CODE
        ];
    }

}