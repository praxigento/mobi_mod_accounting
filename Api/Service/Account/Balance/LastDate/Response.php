<?php
/**
 *
 */

namespace Praxigento\Accounting\Api\Service\Account\Balance\LastDate;

class Response
    extends \Praxigento\Core\App\Service\Response
{
    const LAST_DATE = 'last_date';

    public function getLastDate()
    {
        $result = $this->get(self::LAST_DATE);
        return $result;
    }

    public function setLastDate($data)
    {
        $this->set(self::LAST_DATE, $data);
    }
}
