<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

use Mrself\Mrcommerce\Import\BC\Catalog\Entity\HookRecordInterface;

class HookRecordProcessor
{
    /**
     * @var ImportersManager
     */
    private $importersManager;

    /**
     * @var callable
     */
    private $deleteCallback;

    public function __construct(ImportersManager $importersManager)
    {
        $this->importersManager = $importersManager;
    }

    public function setDeleteCallback(callable $callback)
    {
        $this->deleteCallback = $callback;
    }

    /**
     * @param HookRecordInterface[] $records
     */
    public function processMany(array $records)
    {
        $forImport = [];
        $forDelete = [];

        foreach ($records as $record) {
            if ($record->isCreated() || $record->isUpdated()) {
                $forImport[$record->getResourceId()] = $record;
            } else {
                $forDelete[$record->getResourceId()] = $record;
            }

            $record->makeProcessed();
        }

        $this->import($forImport);
    }

    /**
     * @param HookRecordInterface[] $records
     * @param callable $deleteCallback
     */
    public function processProductRecords(array $records, callable $deleteCallback)
    {
        $forImport = [];
        $forDelete = [];

        foreach ($records as $record) {
            if ($record->isCreated() || $record->isUpdated()) {
                $forImport[$record->getResourceId()] = $record;
            } else {
                $forDelete[$record->getResourceId()] = $record;
            }

            $record->makeProcessed();
        }

        $this->importersManager->getProductImporter()->importByBcIds(array_keys($forImport));
        $deleteCallback(array_keys($forDelete));
    }

    public function processOne(HookRecordInterface $record)
    {
        $importer = $this->importersManager->defineImporter($record->getResourceType());
    }

    /**
     * @param HookRecordInterface[] $forImport
     */
    private function import(array $forImport)
    {
        $records = [];
        foreach ($forImport as $item) {
            $records[] = [
                'type' => $item->getResourceType(),
                'id' => $item->getResourceId(),
            ];
        }
        $this->importersManager->importByBcIds($records);
    }
}