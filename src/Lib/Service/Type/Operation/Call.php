<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Lib\Service\Type\Operation;

class Call extends \Praxigento\Core\Lib\Service\Type\Base\Call implements \Praxigento\Accounting\Lib\Service\ITypeOperation {

    protected function _getResponse() {
        return new Response\GetByCode();
    }

    protected function _getEntityName() {
        return \Praxigento\Accounting\Data\Entity\Type\Operation::ENTITY_NAME;
    }

}