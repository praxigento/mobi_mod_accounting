<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Data\Agg;

/**
 * Aggregate for transactions grid.
 */
class Transaction
    extends \Flancer32\Lib\DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_ASSET = 'Asset';
    const AS_CREDIT = 'Credit';
    const AS_DATE_APPLIED = 'DateApplied';
    const AS_DEBIT = 'Debit';
    const AS_ID_OPER = 'OperId';
    const AS_ID_TRANS = 'TransId';
    const AS_NOTE = 'Note';
    const AS_VALUE = 'Value';
    /**#@- */

    /** @return string */
    public function getAsset()
    {
        $result = parent::getData(self::AS_ASSET);
        return $result;
    }

    /** @return string */
    public function getCredit()
    {
        $result = parent::getData(self::AS_CREDIT);
        return $result;
    }

    /** @return string */
    public function getDateApplied()
    {
        $result = parent::getData(self::AS_DATE_APPLIED);
        return $result;
    }

    /** @return string */
    public function getDebit()
    {
        $result = parent::getData(self::AS_DEBIT);
        return $result;
    }

    /** @return int */
    public function getIdOper()
    {
        $result = parent::getData(self::AS_ID_OPER);
        return $result;
    }

    /** @return int */
    public function getIdTrans()
    {
        $result = parent::getData(self::AS_ID_TRANS);
        return $result;
    }

    /** @return string */
    public function getNote()
    {
        $result = parent::getData(self::AS_NOTE);
        return $result;
    }

    /** @return double */
    public function getValue()
    {
        $result = parent::getData(self::AS_VALUE);
        return $result;
    }

    public function setAsset($data)
    {
        parent::setData(self::AS_ASSET, $data);
    }

    public function setCredit($data)
    {
        parent::setData(self::AS_CREDIT, $data);
    }

    public function setDateApplied($data)
    {
        parent::setData(self::AS_DATE_APPLIED, $data);
    }

    public function setDebit($data)
    {
        parent::setData(self::AS_DEBIT, $data);
    }

    public function setIdOper($data)
    {
        parent::setData(self::AS_ID_OPER, $data);
    }

    public function setIdTrans($data)
    {
        parent::setData(self::AS_ID_TRANS, $data);
    }

    public function setNote($data)
    {
        parent::setData(self::AS_NOTE, $data);
    }

    public function setValue($data)
    {
        parent::setData(self::AS_VALUE, $data);
    }

}