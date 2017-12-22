<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\OnDate;

class Request
    extends \Praxigento\Core\App\Service\Base\Request
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
        $result = $this->get(static::ASSET_TYPE_ID);
        return $result;
    }

    public function getDate()
    {
        $result = $this->get(static::DATE);
        return $result;
    }

    public function setAssetTypeId($data)
    {
        $this->set(static::ASSET_TYPE_ID, $data);
    }

    public function setDate($data)
    {
        $this->set(static::DATE, $data);
    }

}
