<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog;

interface ImportProcessorInterface
{
    public function process($bcResource);
}