<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\LastDate;

class Response
    extends \Praxigento\Core\App\Service\Response
{
    const LAST_DATE = 'last_date';

    public function getLastDate()
    {
        $result = $this->get(static::LAST_DATE);
        return $result;
    }
}
