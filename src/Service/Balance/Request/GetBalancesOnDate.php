<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

class GetBalancesOnDate extends \Praxigento\Core\Service\Base\Request
{
    /**
     * ID of the account's asset type.
     * @var int
     */
    const ASSET_TYPE_ID = 'asset_type_id';
    /**
     * Date as datestamp (YYYYMMDD).
     *
     * @var  string datestamp (YYYYMMDD).
     */
    const DATE = 'date';

    public function getAssetTypeId()
    {
        $result = $this->getData(static::ASSET_TYPE_ID);
        return $result;
    }
    public function setAssetTypeId($data)
    {
        $this->setData(static::ASSET_TYPE_ID, $data);
    }

    public function getDate()
    {
        $result = $this->getData(static::DATE);
        return $result;
    }


    public function setDate($data)
    {
        $this->setData(static::DATE, $data);
    }
}