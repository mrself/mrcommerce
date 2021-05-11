<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

interface AbsentEntitiesRemovingInterface
{
    public function removeAbsentEntities();

    public function resetIsImportedField();
}