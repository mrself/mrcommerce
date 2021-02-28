<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ResourceImportedEvent extends Event
{
    public const NAME = 'mrcommerce.bc.resource.imported';

    private $bcResource;

    private $processorResult;

    public function __construct($bcResource, $processorResult)
    {
        $this->bcResource = $bcResource;
        $this->processorResult = $processorResult;
    }

    /**
     * @return mixed
     */
    public function getBcResource()
    {
        return $this->bcResource;
    }

    /**
     * @return mixed
     */
    public function getProcessorResult()
    {
        return $this->processorResult;
    }
}