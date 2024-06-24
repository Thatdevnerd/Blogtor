<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class AdminController extends AbstractController
{
    //TODO Implement this

    #[Route('/admin/dashboard', name: 'app_admin_admin')]
    public function index(): Response
    {
        throw new NotImplementedException("Implement this");
    }
}
