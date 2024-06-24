<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_admin')]
    public function index(Request $request, userInterface $user): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
