<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Hooks;

use Bigcommerce\Api\Client as Bigcommerce;
use Mrself\Mrcommerce\BC\BigcommerceV2Configurator;

class HooksManager
{
    /**
     * @var string
     */
    private $hooksDestUrl;

    public function __construct(BigcommerceV2Configurator $configurator, string $hooksDestUrl)
    {
        $configurator->configure();
        $this->hooksDestUrl = $hooksDestUrl;
    }

    public function createProductHooks()
    {
        $this->createHook('product', 'created');
        $this->createHook('product', 'updated');
    }

    private function createHook(string $resource, string $scope)
    {
        Bigcommerce::createWebhook([
            'scope' => 'store/' . $resource . '/' . $scope,
            'destination' => $this->hooksDestUrl . '/hooks/product/' . $scope,
            'active' => true,
        ]);
    }

    public function listHooks()
    {
        $hooks = Bigcommerce::listWebhooks();
        dump($hooks);
    }
}