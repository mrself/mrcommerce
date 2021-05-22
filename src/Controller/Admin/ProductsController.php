<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends AbstractController
{
    public function index(): Response
    {
        return new Response();
    }
}