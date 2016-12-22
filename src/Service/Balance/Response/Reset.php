<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Service\Balance\Response;

class Reset extends \Praxigento\Core\Service\Base\Response
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