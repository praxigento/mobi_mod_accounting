<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Operation;

use Praxigento\Accounting\Data\Agg\Operation as AggEntity;
use Praxigento\Accounting\Data\Entity\Operation as EOperation;
use Praxigento\Accounting\Data\Entity\Type\Operation as ETypeOper;
use Praxigento\Accounting\Repo\Agg\IOperation as AggRepo;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class SelectFactory
    implements \Praxigento\Core\Repo\Query\IHasSelect
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
    }

    /** @inheritdoc */
    public function getQueryToSelectCount()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asOper = AggRepo::AS_OPER;
        $asType = AggRepo::AS_TYPE;
        //
        $tblOper = [$asOper => $this->_resource->getTableName(EOperation::ENTITY_NAME)];
        $tblType = [$asType => $this->_resource->getTableName(ETypeOper::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $expValue = 'COUNT(' . $asOper . '.' . EOperation::ATTR_ID . ')';
        $cols = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $result->from($tblOper, $cols);
        /* LEFT JOIN prxgt_acc_type_operation */
        $cond = $asType . '.' . ETypeOper::ATTR_ID . '=' . $asOper . '.' . EOperation::ATTR_TYPE_ID;
        $cols = [];
        $result->joinLeft($tblType, $cond, $cols);
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asOper = AggRepo::AS_OPER;
        $asType = AggRepo::AS_TYPE;
        //
        $tblOper = [$asOper => $this->_resource->getTableName(EOperation::ENTITY_NAME)];
        $tblType = [$asType => $this->_resource->getTableName(ETypeOper::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $cols = [
            AggEntity::AS_ID => EOperation::ATTR_ID,
            AggEntity::AS_DATE_PERFORMED => EOperation::ATTR_DATE_PREFORMED,
            AggEntity::AS_NOTE => EOperation::ATTR_NOTE
        ];
        $result->from($tblOper, $cols);
        /* LEFT JOIN prxgt_acc_type_operation */
        $cond = $asType . '.' . ETypeOper::ATTR_ID . '=' . $asOper . '.' . EOperation::ATTR_TYPE_ID;
        $cols = [
            AggEntity::AS_TYPE => ETypeOper::ATTR_CODE
        ];
        $result->joinLeft($tblType, $cond, $cols);
        return $result;
    }
}