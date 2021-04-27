<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Exception;

use Mrself\Mrcommerce\Import\BC\Catalog\ImportProcessorInterface;
use Mrself\Mrcommerce\MrcommerceException;

class RemoveAbsentMethodNotExistException extends MrcommerceException
{
    /**
     * @var ImportProcessorInterface
     */
    private $processor;

    public function __construct(ImportProcessorInterface $processor)
    {
        $this->processor = $processor;

        parent::__construct('The method "removeAbsentEntities" does not exist in the provided import processor');
    }

    /**
     * @return ImportProcessorInterface
     */
    public function getProcessor(): ImportProcessorInterface
    {
        return $this->processor;
    }
}