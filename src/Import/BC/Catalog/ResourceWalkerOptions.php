<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Api\CatalogApi;

class ResourceWalkerOptions
{
    public const INCLUDE_RESOURCE_IMAGES = 'images';
    public const INCLUDE_RESOURCE_OPTIONS = 'options';
    public const INCLUDE_RESOURCE_VARIANTS = 'variants';
    public const INCLUDE_RESOURCE_PRIMARY_IMAGE = 'primary_image';

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

    public $includeResources = [];

    public function __construct(CatalogApi $catalogApi)
    {
        $this->client = $catalogApi;
    }
}