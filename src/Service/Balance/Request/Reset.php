<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Request;

class Reset extends \Praxigento\Core\Service\Base\Request
{
    /**
     * Reset balance starting from this date (including).
     *
     * @var  string datestamp (YYYYMMDD).
     */
    const DATE_FROM = 'date_from';

    public function getDateFrom()
    {
        $result = $this->getData(static::DATE_FROM);
        return $result;
    }

    public function setDateFrom($data)
    {
        $this->setData(static::DATE_FROM, $data);
    }
}