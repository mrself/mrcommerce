<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Api\CatalogApi;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourceImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourcesImportedEvent;
use Mrself\Mrcommerce\Import\BC\ResourceWalker;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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

    /**
     * @var ImportProcessorInterface
     */
    private $importProcessor;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var int
     */
    private $resourceLimit = ResourceWalker::MAX_RESOURCE_LIMIT;

    public function __construct(CatalogApi $catalogApi, ResourceWalker $walker, EventDispatcherInterface $eventDispatcher)
    {
        $this->catalogApi = $catalogApi;
        $this->walker = $walker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param ImportProcessorInterface $importProcessor
     */
    public function setImportProcessor(ImportProcessorInterface $importProcessor): void
    {
        $this->importProcessor = $importProcessor;
    }

    public function importByBcId(int $bcId)
    {
        $bcResource = $this->getBcResource($bcId);
    }

    public function importByBcIds(array $ids, ?int $resourceLimit = null)
    {
        $resourceLimit = $resourceLimit ?? $this->resourceLimit;
        if (count($ids) > $resourceLimit) {
            for ($i = 0; $i <= $resourceLimit; $i++) {
                $idsPart = array_slice($ids, $i * $resourceLimit, $resourceLimit);
                $this->importByBcIds($idsPart);
            }
            return;
        }

        $method = $this->getMethodMultiple();
        $resources = $this->catalogApi->$method([
            'id:in' => $ids
        ]);
        $this->importResources($resources->getData());
    }

    public function importResources(array $resources)
    {
        $this->importProcessor->startImportResources($resources);

        foreach ($resources as $resource) {
            $this->importResource($resource);
        }

        $event = new ResourcesImportedEvent($resources);
        $this->eventDispatcher->dispatch($event, $event::NAME);
        $this->importProcessor->endImportResources($resources);
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
        if (!$this->shouldBeImported($bcResource)) {
            return null;
        }

        $this->importProcessor->process($bcResource);
        $this->dispatchEvent($bcResource);
    }

    protected function shouldBeImported($bcResource): bool
    {
        return true;
    }

    protected function dispatchEvent($bcResource)
    {
        $event = new ResourceImportedEvent($bcResource);
        $this->eventDispatcher->dispatch($event, $event::NAME);
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