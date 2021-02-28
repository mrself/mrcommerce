<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use Mrself\Mrcommerce\Import\BC\Catalog\Product\ProductImporter;

class ImportersManager
{
    public const TYPE_PRODUCT = 0;

    /**
     * @var ProductImporter
     */
    private $productImporter;

    /**
     * @var array
     */
    private $importersMap = [];

    public function __construct(ProductImporter $productImporter)
    {
        $this->productImporter = $productImporter;

        $this->importersMap[static::TYPE_PRODUCT] = $productImporter;
    }

    public function defineImporter(int $type)
    {
        return $this->importersMap[$type];
    }

    /**
     * @param array[] $recordItems
     */
    public function importByBcIds(array $recordItems)
    {

    }
}