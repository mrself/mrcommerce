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
        $this->walker->configureOptions(function (ResourceWalkerOptions $options) {
            $options->callback = function ($resource) {
                $this->importBatchResource($resource);
            };

            $options->byOne = true;
            $options->apiMethod = $this->getMethodMultiple();
            $options->queryParams = $this->getWalkerQueryParams();
        });

        $this->walker->walk();
    }

    protected function importBatchResource($bcResource) {
        $this->importResource($bcResource);
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