<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use BigCommerce\Api\v3\Model\Product as BcProduct;
use Mrself\Mrcommerce\Entity\EntityInterface;
use Mrself\Mrcommerce\Repository\Catalog\ImportProcessor\RepositoryInterface;

class RepositoryImportProcessor extends AbstractImportProcessor implements ImportProcessorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
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

    private function sync($bcProduct, EntityInterface $product)
    {

    }

    private function find(BcProduct $bcProduct): ?EntityInterface
    {
        return $this->repository->findOneByBcId($bcProduct->getId());
    }

    public function processBatchResource($bcResource)
    {
        $entity = $this->repository->createEntity();
        $entity->setBcId($bcResource->getId());

        if (method_exists($this->repository, 'importBcResource')) {
            $this->repository->importBcResource($bcResource);
        }

        $this->repository->save($entity);
    }

}