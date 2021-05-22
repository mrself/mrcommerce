<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Helpers;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Mrself\Mrcommerce\MrcommerceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestingKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', false);
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new MrcommerceBundle(),
        ];
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__ . '/../../config/routes.yaml');
    }

    public function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__.'/config.yaml', 'yaml');
    }
}