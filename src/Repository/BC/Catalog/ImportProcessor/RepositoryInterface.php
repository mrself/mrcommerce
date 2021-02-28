<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Repository\BC\Catalog\ImportProcessor;

interface RepositoryInterface
{
    public function save(EntityInterface $entity);

    public function createEntity(): EntityInterface;
}