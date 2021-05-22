<?php declare(strict_types=1);

namespace Mrself\Mrcommerce;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MrcommerceBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}