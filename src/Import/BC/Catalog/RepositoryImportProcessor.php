<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Model\Product as BcProduct;
use Mrself\Mrcommerce\Entity\EntityInterface;
use Mrself\Mrcommerce\Import\BC\Sync\SyncInterface;
use Mrself\Mrcommerce\MrcommerceException;
use Mrself\Mrcommerce\Repository\Catalog\ImportProcessor\AbsentEntitiesRemovingInterface as RepositoryAbsentEntitiesRemovingInterface;
use Mrself\Mrcommerce\Repository\Catalog\ImportProcessor\RepositoryInterface;

class RepositoryImportProcessor extends AbstractImportProcessor implements ImportProcessorInterface
{
    /**
     * @var RepositoryInterface|RepositoryAbsentEntitiesRemovingInterface
     */
    protected $repository;

    /**
     * @var SyncInterface
     */
    private $sync;

    public function __construct(RepositoryInterface $repository, SyncInterface $sync)
    {
        $this->repository = $repository;
        $this->sync = $sync;
    }

    public function process($bcResource)
    {
        $product = $this->findOrCreate($bcResource);
        $this->sync($bcResource, $product);
        $this->repository->save($product);
    }

    private function findOrCreate($bcResource): EntityInterface
    {
        $entity = $this->find($bcResource);

        if (!$entity) {
            $entity = $this->repository->createEntity();
        }

        return $entity;
    }

    private function sync($bcResource, EntityInterface $entity)
    {
        $this->sync->sync($bcResource, $entity);
    }

    private function find($bcResource): ?EntityInterface
    {
        return $this->repository->findOneByBcId($bcResource->getId());
    }

    public function processBatchResource($bcResource)
    {
        $entity = $this->findOrCreate($bcResource);
        $this->sync($bcResource, $entity);

        if (method_exists($this->repository, 'importBcResource')) {
            $this->repository->importBcResource($bcResource);
        }

        $this->saveBatchResource($bcResource, $entity);
    }

    protected function saveBatchResource($bcResource, EntityInterface $entity)
    {

    }

    public function endImportResources(array $resources)
    {
        if (method_exists($this->repository, 'onBatchEnd')) {
            $this->repository->onBatchEnd();
        }
    }

    public function removeAbsentEntities()
    {
        $this->ensureHavingAbsentEntitiesRemovingInterface();
        $this->repository->removeBigcommerceNotSyncedEntities();
    }

    public function resetIsImportedField()
    {
        $this->ensureHavingAbsentEntitiesRemovingInterface();
        $this->repository->resetIsImportedField();
    }

    private function ensureHavingAbsentEntitiesRemovingInterface()
    {
        if ($this instanceof AbsentEntitiesRemovingInterface) {
            if (!($this->repository instanceof RepositoryAbsentEntitiesRemovingInterface)) {
                throw new MrcommerceException('Repository should implement AbsentEntitiesRemovingInterface');
            }
        }
    }
}