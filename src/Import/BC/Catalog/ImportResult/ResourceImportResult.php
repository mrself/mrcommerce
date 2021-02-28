<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\ImportResult;

class ResourceImportResult
{
    private $bcResource;

    /**
     * @var bool
     */
    private $isProcessed = false;

    private $processorResult;

    public function __construct($bcResource, $processorResult)
    {
        $this->bcResource = $bcResource;

        $this->processorResult = $processorResult;
        if ($processorResult) {
            $this->isProcessed = true;
        }
    }

    /**
     * @return mixed
     */
    public function getBcResource()
    {
        return $this->bcResource;
    }

    /**
     * @return bool
     */
    public function isProcessed(): ?bool
    {
        return $this->isProcessed;
    }

    /**
     * @return mixed
     */
    public function getProcessorResult()
    {
        return $this->processorResult;
    }
}