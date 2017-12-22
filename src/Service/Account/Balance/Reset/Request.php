<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\Reset;

class Request
    extends \Praxigento\Core\App\Service\Base\Request
{
    /**
     * Reset balance starting from this date (including, Magento time).
     *
     * @var  string datestamp (YYYYMMDD).
     */
    const DATE_FROM = 'date_from';

    public function getDateFrom()
    {
        $result = $this->get(static::DATE_FROM);
        return $result;
    }

    public function setDateFrom($data)
    {
        $this->set(static::DATE_FROM, $data);
    }

}
