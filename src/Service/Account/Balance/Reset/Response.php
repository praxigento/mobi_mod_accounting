<?php
/**
 *
 */

namespace Praxigento\Accounting\Service\Account\Balance\Reset;

class Response
    extends \Praxigento\Core\App\Service\Base\Response
{
    const ROWS_DELETED = 'rows_deleted';

    public function getRowsDeleted()
    {
        $result = $this->get(self::ROWS_DELETED);
        return $result;
    }

    public function setRowsDeleted($data)
    {
        $this->set(self::ROWS_DELETED, $data);
    }

}
