<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use Mrself\Mrcommerce\Repository\BC\Catalog\ImportProcessor\RepositoryInterface;

class RepositoryImportProcessor extends AbstractImportProcessor implements ImportProcessorInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function process($bcResource)
    {
        $entity = $this->repository->createEntity();
        $entity->setBcId($bcResource->getId());
        $this->repository->save($entity);
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