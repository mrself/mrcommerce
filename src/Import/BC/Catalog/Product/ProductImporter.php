<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Product;

use BigCommerce\Api\v3\Api\CatalogApi;
use Mrself\Mrcommerce\Import\BC\Catalog\AbstractImporter;
use Mrself\Mrcommerce\Import\BC\Catalog\ImporterInterface;

class ProductImporter extends AbstractImporter implements ImporterInterface
{
    protected function getMethodMultiple(): string
    {
        return 'getProducts';
    }

    protected function getMethodSingle(): string
    {
        return 'product';
    }
}