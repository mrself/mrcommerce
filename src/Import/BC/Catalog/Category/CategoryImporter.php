<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Category;

use Mrself\Mrcommerce\Import\BC\Catalog\AbstractImporter;
use Mrself\Mrcommerce\Import\BC\Catalog\ImporterInterface;

class CategoryImporter extends AbstractImporter implements ImporterInterface
{
    protected function getMethodSingle(): string
    {
        return 'getCategoryById';
    }

    protected function getMethodMultiple(): string
    {
        return 'getCategories';
    }

}