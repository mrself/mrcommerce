<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Sync;

use Mrself\Mrcommerce\Entity\EntityInterface;

interface SyncInterface
{
    public function sync($bcResource, EntityInterface $entity, bool $isNew);
}