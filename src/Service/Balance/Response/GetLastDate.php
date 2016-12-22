<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Response;

class GetLastDate extends \Praxigento\Core\Service\Base\Response {
    const LAST_DATE = 'last_date';

    public function getLastDate() {
        $result = $this->get(static::LAST_DATE);
        return $result;
    }
}