<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Agg\Def\Operation;

use Praxigento\Accounting\Data\Agg\Operation as AggEntity;
use Praxigento\Accounting\Data\Entity\Operation as EntityOperation;
use Praxigento\Accounting\Data\Entity\Type\Operation as EntityTypeOper;
use Praxigento\Accounting\Repo\Agg\IOperation as AggRepo;

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
        $tblOper = [$asOper => $this->_resource->getTableName(EntityOperation::ENTITY_NAME)];
        $tblType = [$asType => $this->_resource->getTableName(EntityTypeOper::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $expValue = 'COUNT(' . $asOper . '.' . EntityOperation::ATTR_ID . ')';
        $cols = new \Praxigento\Core\Repo\Query\Expression($expValue);
        $result->from($tblOper, $cols);
        /* LEFT JOIN prxgt_acc_type_operation */
        $on = $asType . '.' . EntityTypeOper::ATTR_ID . '=' . $asOper . '.' . EntityOperation::ATTR_TYPE_ID;
        $cols = [];
        $result->joinLeft($tblType, $on, $cols);
        return $result;
    }

    /** @inheritdoc */
    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asOper = AggRepo::AS_OPER;
        $asType = AggRepo::AS_TYPE;
        //
        $tblOper = [$asOper => $this->_resource->getTableName(EntityOperation::ENTITY_NAME)];
        $tblType = [$asType => $this->_resource->getTableName(EntityTypeOper::ENTITY_NAME)];
        /* SELECT FROM prxgt_acc_account */
        $cols = [
            AggEntity::AS_ID => EntityOperation::ATTR_ID,
            AggEntity::AS_DATE_PERFORMED => EntityOperation::ATTR_DATE_PREFORMED,
            AggEntity::AS_NOTE => EntityOperation::ATTR_NOTE
        ];
        $result->from($tblOper, $cols);
        /* LEFT JOIN prxgt_acc_type_operation */
        $on = $asType . '.' . EntityTypeOper::ATTR_ID . '=' . $asOper . '.' . EntityOperation::ATTR_TYPE_ID;
        $cols = [
            AggEntity::AS_TYPE => EntityTypeOper::ATTR_CODE
        ];
        $result->joinLeft($tblType, $on, $cols);
        return $result;
    }
}