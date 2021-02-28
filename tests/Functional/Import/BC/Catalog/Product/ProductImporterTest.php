<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Import\BC\Catalog\Product;

use Mrself\Mrcommerce\Import\BC\Catalog\Product\ProductImporter;
use Mrself\Mrcommerce\Tests\Helpers\TestCase;

class ProductImporterTest extends TestCase
{
    /**
     * @var ProductImporter
     */
    private $importer;

    public function testBase()
    {
        // @todo Implement
        $this->expectNotToPerformAssertions();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = $this->container->get(ProductImporter::class);
    }
}