<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Repository\BC\Catalog\ImportProcessor;

interface EntityInterface
{
    public function setBcId(int $bcId): void;
}