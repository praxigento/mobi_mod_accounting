<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\Reset;

class Request
    extends \Praxigento\Core\App\Service\Request
{
    const ACCOUNTS = 'accounts';
    const ASSET_TYPES = 'asset_types';
    const DATE_FROM = 'date_from';

    /**
     * Reset balances for the given accounts.
     *
     * @return int[]|null
     */
    public function getAccounts()
    {
        $result = $this->get(self::ACCOUNTS);
        return $result;
    }

    /**
     * Reset balances for the given asset types only.
     *
     * @return int[]|null
     */
    public function getAssetTypes()
    {
        $result = $this->get(self::ASSET_TYPES);
        return $result;
    }

    /**
     * Reset balance starting from this date (excluding, datestamp - YYYYMMDD).
     *
     * @return string
     */
    public function getDateFrom()
    {
        $result = $this->get(self::DATE_FROM);
        return $result;
    }

    /**
     * Reset balances for the given accounts.
     *
     * @param int[] $data
     * @return void
     */
    public function setAccounts($data)
    {
        $this->set(self::ACCOUNTS, $data);
    }

    /**
     * Reset balances for the given asset types only.
     *
     * @param int[] $data
     * @return void
     */
    public function setAssetTypes($data)
    {
        $this->set(self::ASSET_TYPES, $data);
    }

    /**
     * Reset balance starting from this date (excluding, datestamp - YYYYMMDD).
     *
     * @param $data
     * @return void
     */
    public function setDateFrom($data)
    {
        $this->set(self::DATE_FROM, $data);
    }

}
