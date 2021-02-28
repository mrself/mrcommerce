<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use Mrself\Mrcommerce\Import\BC\Catalog\Entity\HookRecordInterface;

class HookRecordProcessor
{
    /**
     * @var ImportersManager
     */
    private $importersManager;

    public function __construct(ImportersManager $importersManager)
    {
        $this->importersManager = $importersManager;
    }

    /**
     * @param HookRecordInterface[] $records
     */
    public function processMany(array $records)
    {
        foreach ($records as $record) {
            $this->processOne($record);
            $record->makeProcessed();
        }
    }

    public function processOne(HookRecordInterface $record)
    {
        $importer = $this->importersManager->defineImporter($record->getResourceType());
    }
}