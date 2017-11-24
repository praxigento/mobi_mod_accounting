<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response;
class Data
    extends \Praxigento\Core\Data
{
    const ASSETS = 'assets';
    const CUSTOMER = 'customer';

    /**
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset[]
     */
    public function getAssets()
    {
        $result = parent::get(self::ASSETS);
        return $result;
    }

    /**
     * @return \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Customer
     */
    public function getCustomer()
    {
        $result = parent::get(self::CUSTOMER);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Asset[] $data
     */
    public function setAssets($data)
    {
        parent::set(self::ASSETS, $data);
    }

    /**
     * @param \Praxigento\Accounting\Api\Service\Asset\Transfer\Init\Response\Data\Customer $data
     */
    public function setCustomer($data)
    {
        parent::set(self::CUSTOMER, $data);
    }
}