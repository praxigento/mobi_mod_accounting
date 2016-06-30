<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Accounting\Repo\Entity\Type\Def;

class Asset
    extends \Praxigento\Core\Repo\Entity\Def\Type
    implements \Praxigento\Accounting\Repo\Entity\Type\IAsset
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct(
            $resource,
            $repoGeneric,
            \Praxigento\Accounting\Data\Entity\Type\Asset::class
        );
    }
}