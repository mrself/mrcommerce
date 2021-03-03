<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Repository\Catalog\ImportProcessor;

use Mrself\Mrcommerce\Entity\EntityInterface;

interface RepositoryInterface
{
    public function save(EntityInterface $entity);

    public function createEntity(): EntityInterface;

    public function findOneByBcId(int $bcId): ?EntityInterface;
}