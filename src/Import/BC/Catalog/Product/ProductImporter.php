<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Product;

use BigCommerce\Api\v3\Api\CatalogApi;
use Mrself\Mrcommerce\Import\BC\Catalog\AbstractImporter;

class ProductImporter extends AbstractImporter
{
    protected function getMethodMultiple(): string
    {
        return 'getProducts';
    }

}