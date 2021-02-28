<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ResourceImportedEvent extends Event
{
    public const NAME = 'mrcommerce.bc.resource.imported';

    private $bcResource;

    public function __construct($bcResource)
    {
        $this->bcResource = $bcResource;
    }

    /**
     * @return mixed
     */
    public function getBcResource()
    {
        return $this->bcResource;
    }
}