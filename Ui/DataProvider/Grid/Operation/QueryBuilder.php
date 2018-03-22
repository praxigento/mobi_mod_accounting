<?php

namespace Praxigento\Accounting\Ui\DataProvider\Grid\Operation;

use Praxigento\Accounting\Repo\Data\Operation as EOperation;
use Praxigento\Accounting\Repo\Data\Type\Operation as ETypeOper;

class QueryBuilder
    extends \Praxigento\Core\App\Ui\DataProvider\Grid\Query\Builder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_OPER = 'pao';
    const AS_TYPE = 'pato';
    /**#@- */

    /**#@+
     * Aliases for data attributes.
     */
    const A_DATE_PERFORMED = 'datePerformed';
    const A_ID = 'id';
    const A_NOTE = 'note';
    const A_TYPE = 'type';

    /**#@- */

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            $map = [
                self::A_DATE_PERFORMED => self::AS_OPER . '.' . EOperation::ATTR_DATE_PREFORMED,
                self::A_ID => self::AS_OPER . '.' . EOperation::ATTR_ID,
                self::A_NOTE => self::AS_OPER . '.' . EOperation::ATTR_NOTE,
                self::A_TYPE => self::AS_TYPE . '.' . ETypeOper::ATTR_CODE
            ];
            $this->mapper = new \Praxigento\Core\App\Repo\Query\Criteria\Def\Mapper($map);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asOper = self::AS_OPER;
        $asType = self::AS_TYPE;

        /* SELECT FROM prxgt_acc_operation */
        $tbl = $this->resource->getTableName(EOperation::ENTITY_NAME);
        $as = $asOper;
        $cols = [
            self::A_ID => EOperation::ATTR_ID,
            self::A_DATE_PERFORMED => EOperation::ATTR_DATE_PREFORMED,
            self::A_NOTE => EOperation::ATTR_NOTE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_type_operation */
        $tbl = $this->resource->getTableName(ETypeOper::ENTITY_NAME);
        $as = $asType;
        $cond = $asType . '.' . ETypeOper::ATTR_ID . '=' . $asOper . '.' . EOperation::ATTR_TYPE_ID;
        $cols = [
            self::A_TYPE => ETypeOper::ATTR_CODE
        ];
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* return  result */
        return $result;
    }

    protected function getQueryTotal()
    {
        /* get query to select items */
        /** @var \Magento\Framework\DB\Select $result */
        $result = $this->getQueryItems();
        /* ... then replace "columns" part with own expression */
        $value = 'COUNT(' . self::AS_OPER . '.' . EOperation::ATTR_ID . ')';

        /**
         * See method \Magento\Framework\DB\Select\ColumnsRenderer::render:
         */
        /**
         * if ($column instanceof \Zend_Db_Expr) {...}
         */
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($value);
        /**
         *  list($correlationName, $column, $alias) = $columnEntry;
         */
        $entry = [null, $exp, null];
        $cols = [$entry];
        $result->setPart('columns', $cols);
        return $result;
    }
}