<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Accounting\Service\Balance\Get;

/**
 * @property string $dateFrom balance period begin ('20170101', including)
 * @property string $dateTo balance period end ('20171231', including)
 * @property int $assetTypeId
 * @property string $assetTypeCode
 */
class Request
    extends \Praxigento\Core\Service\Base\Request
{

}