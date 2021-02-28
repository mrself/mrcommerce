<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

class ArrayImportProcessor implements ImportProcessorInterface
{
    /**
     * @var array
     */
    private $resources = [];

    public function process($bcResource)
    {
        $this->resources[$bcResource->getId()] = $bcResource;
    }

    public function hasImportedById($id): bool
    {
        return array_key_exists($id, $this->resources);
    }

}