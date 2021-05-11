<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Repository\Catalog\ImportProcessor;

interface AbsentEntitiesRemovingInterface
{
    public function removeBigcommerceNotSyncedEntities();

    public function resetIsImportedField();
}