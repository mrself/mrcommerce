<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Api\CatalogApi;
use Mrself\Mrcommerce\Import\BC\ResourceWalker;

abstract class AbstractImporter
{
    /**
     * @var CatalogApi
     */
    protected $catalogApi;

    /**
     * @var ResourceWalker
     */
    private $walker;

    public function __construct(CatalogApi $catalogApi, ResourceWalker $walker)
    {
        $this->catalogApi = $catalogApi;
        $this->walker = $walker;
    }

    public function importByBcId(int $bcId)
    {
        $bcResource = $this->getBcResource($bcId);
    }

    public function importAll()
    {
        ResourceWalker::make([
            'callback' => function ($resource) {
                return $this->importResource($resource);
            },
            'byOne' => true,
            'method' => $this->getMethodMultiple(),
            'queryParams' => $this->getWalkerQueryParams()
        ])->walk();
    }

    public function importResource($bcResource)
    {

    }

    protected function getBcResource(int $bcId)
    {

    }

    abstract protected function getMethodMultiple(): string;

    protected function getWalkerQueryParams(): array
    {
        return [];
    }
}