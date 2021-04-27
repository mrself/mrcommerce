<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

class ArrayImportProcessor implements ImportProcessorInterface
{
    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var array
     */
    private $batchResources = [];

    /**
     * @var bool
     */
    private $skipAll;

    /**
     * @var bool
     */
    public $absentEntitiesRemoved = false;

    public function process($bcResource)
    {
        $this->resources[$bcResource->getId()] = $bcResource;
    }

    public function hasImportedById($id): bool
    {
        return array_key_exists($id, $this->resources);
    }

    public function startImportResources(array $resources)
    {
    }

    public function endImportResources(array $resources)
    {
    }

    public function processBatchResource($bcResource)
    {
        $this->batchResources[$bcResource->getId()] = $bcResource;
    }

    /**
     * @return array
     */
    public function getBatchResources(): ?array
    {
        return $this->batchResources;
    }

    public function hasBatchImportedById(int $id): bool
    {
        return array_key_exists($id, $this->batchResources);
    }

    public function shouldBeImported($bcResource): bool
    {
        if (is_null($this->skipAll)) {
            return true;
        }

        return !$this->skipAll;
    }

    public function setSkipAll(bool $value)
    {
        $this->skipAll = true;
    }

    public function getRemoveAbsentEntities(): bool
    {
        return true;
    }

    public function removeAbsentEntities()
    {
        $this->absentEntitiesRemoved = true;
    }
}