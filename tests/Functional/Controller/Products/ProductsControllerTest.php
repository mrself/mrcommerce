<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Controller\Products;

use Mrself\Mrcommerce\Tests\Helpers\TestCase;
use Mrself\Mrcommerce\Tests\Helpers\TestingKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsControllerTest extends WebTestCase
{
    public function testIndexRoute()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/products');
    }

    protected function setUp(): void
    {
        parent::setUp();

//        $kernel = static::bootKernel();
//        $kernel = new TestingKernel();
//        $kernel->boot();
    }
}