<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Api\CatalogApi;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\BatchResourceImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourceImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourcesImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Exception\ResourceNotFoundException;
use Mrself\Mrcommerce\Import\BC\Catalog\ImportResult\ResourceImportResult;
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
        if (!$this->shouldBeImported($bcResource)) {
            return new ResourceImportResult($bcResource, null);
        }

        $processorResult = $this->importProcessor->process($bcResource);
        $event = new ResourceImportedEvent($bcResource, $processorResult);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        return new ResourceImportResult($bcResource, $processorResult);
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
        $resources = $this->queryResourcesByIds($method, $ids);
        $this->importResources($resources->getData());
    }

    protected function queryResourcesByIds(string $method, array $ids)
    {
        return $this->catalogApi->$method([
            'id:in' => $ids
        ]);
    }

    public function importResources(array $resources)
    {
        $this->importProcessor->startImportResources($resources);

        foreach ($resources as $resource) {
            $this->importBatchResource($resource);
        }

        $event = new ResourcesImportedEvent($resources);
        $this->eventDispatcher->dispatch($event, $event::NAME);
        $this->importProcessor->endImportResources($resources);
    }

    public function importAll()
    {
        $this->walker->configureOptions(function (ResourceWalkerOptions $options) {
            $options->callback = function ($resources) {
                $this->importResources($resources);
            };

            $options->byOne = false;
            $options->apiMethod = $this->getMethodMultiple();
            $options->queryParams = $this->getWalkerQueryParams();

            $this->configureResourceWalkerOptions($options);
        });

        $this->walker->walk();
    }

    protected function importBatchResource($bcResource): ResourceImportResult
    {
        if (!$this->shouldBeImported($bcResource)) {
            return $this->getBatchResourceImportResult($bcResource, null);
        }

        $processorResult = $this->importProcessor->processBatchResource($bcResource);
        return $this->getBatchResourceImportResult($bcResource, $processorResult);
    }

    protected function getBatchResourceImportResult($bcResource, $processorResult): ResourceImportResult
    {
        $result = new ResourceImportResult($bcResource, $processorResult);
        $event = new BatchResourceImportedEvent($result);
        $this->eventDispatcher->dispatch($event, $event::NAME);
        return $result;
    }

    protected function shouldBeImported($bcResource): bool
    {
        if (method_exists($this->importProcessor, 'shouldBeImported')) {
            return $this->importProcessor->shouldBeImported($bcResource);
        }

        return true;
    }

    protected function getBcResource(int $bcId)
    {
        $method = $this->getMethodSingle();
        $resource = $this->findResource($bcId, $method)->getData();

        if ($resource) {
            return $resource;
        }

        throw new ResourceNotFoundException($bcId);
    }

    protected function findResource(int $bcId, string $method)
    {
        return $this->catalogApi->$method($bcId)->getData();
    }

    abstract protected function getMethodSingle(): string;

    abstract protected function getMethodMultiple(): string;

    protected function getWalkerQueryParams(): array
    {
        return [];
    }

    protected function configureResourceWalkerOptions(ResourceWalkerOptions $options)
    {
    }
}