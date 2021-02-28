<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Product;

use BigCommerce\Api\v3\Api\CatalogApi;

class ProductImporter
{
    /**
     * @var CatalogApi
     */
    private CatalogApi $catalogApi;

    public function __construct(CatalogApi $catalogApi)
    {
        $this->catalogApi = $catalogApi;
    }
}