<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Form\BlogPostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LandingController extends AbstractController
{
    #[Route('/landing', name: 'app_landing')]
    public function index(): Response
    {
        //db mock
        $navItems = [
            ['name' => 'Home', 'url' => '/', 'allowed' => 0],
            ['name' => 'Post', 'url' => '/post', 'allowed' => 0]
        ];

        $blogForm = $this->createForm(BlogPostFormType::class);

        if ($blogForm->isSubmitted() && $blogForm->isValid()) {
            $blogs = new Blogs();
            return new JsonResponse(['message' => 'Blog post created!'], 200);
        }

        //fetch allowed nev items from db?
        return $this->render('landing/index.html.twig', [
            'navItems' => $navItems,
            'blogForm' => $blogForm->createView()
        ]);
    }
}
