<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ResourcesImportedEvent extends Event
{
    public const NAME = 'mrcommerce.bc.resources.imported';

    /**
     * @var array
     */
    private $resources;

    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * @return array
     */
    public function getResources(): array
    {
        return $this->resources;
    }
}