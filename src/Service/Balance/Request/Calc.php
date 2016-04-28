<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

class Calc extends \Praxigento\Core\Service\Base\Request
{
    /**
     * ID of the account's asset type.
     * @var int
     */
    const ASSET_TYPE_ID = 'asset_type_id';
    /**
     * Calculate balances up to this date (including).
     *
     * @var  string datestamp (YYYYMMDD).
     */
    const DATE_TO = 'date_to';

    public function getAssetTypeId()
    {
        $result = $this->getData(static::ASSET_TYPE_ID);
        return $result;
    }

    public function getDateTo()
    {
        $result = $this->getData(static::DATE_TO);
        return $result;
    }

    public function setAssetTypeId($data)
    {
        $this->setData(static::ASSET_TYPE_ID, $data);
    }

    public function setDateTo($data)
    {
        $this->setData(static::DATE_TO, $data);
    }
}