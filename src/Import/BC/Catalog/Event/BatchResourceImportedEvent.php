<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Event;

use Mrself\Mrcommerce\Import\BC\Catalog\ImportResult\ResourceImportResult;
use Symfony\Contracts\EventDispatcher\Event;

class BatchResourceImportedEvent extends Event
{
    public const NAME = 'mrcommerce.bc.batch.resource.imported';

    /**
     * @var ResourceImportResult
     */
    private $result;

    public function __construct(ResourceImportResult $result)
    {
        $this->result = $result;
    }

    /**
     * @return ResourceImportResult
     */
    public function getResult(): ResourceImportResult
    {
        return $this->result;
    }

}