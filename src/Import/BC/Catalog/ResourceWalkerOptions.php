<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Api\CatalogApi;

class ResourceWalkerOptions
{
    /**
     * @var CatalogApi
     */
    public $client;

    /**
     * @var array
     */
    public $queryParams = [];

    /**
     * @var array
     */
    public $resourceParams = [];

    /**
     * @var string
     */
    public $apiMethod;

    /**
     * @var bool
     */
    public $byOne = true;

    /**
     * @var callable
     */
    public $callback;

    public function __construct(CatalogApi $catalogApi)
    {
        $this->client = $catalogApi;
    }
}