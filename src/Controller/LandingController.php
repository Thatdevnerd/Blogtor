<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Form\BlogPostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LandingController extends AbstractController
{
    #[Route('/landing', name: 'app_landing')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $blogForm = $this->createForm(BlogPostFormType::class);
        $blogForm->handleRequest($request);

        if ($blogForm->isSubmitted() && $blogForm->isValid()) {
            $blogs = new Blogs();

            $blogs->setTitle($blogForm->get('title')->getData());
            $blogs->setContent($blogForm->get('content')->getData());
            $blogs->setDate($blogForm->get('date')->getData());

            $em->persist($blogs);
            $em->flush();

            return new JsonResponse(['message' => 'Blog post created!', 'data' => [
                'title' => $blogs->getTitle(),
                'content' => $blogs->getContent(),
                'date' => $blogs->getDate()
            ]], 200);
        }

        $navItems = [
            ['name' => 'Home', 'url' => '/', 'allowed' => 0],
            ['name' => 'Post', 'url' => '/post', 'allowed' => 0]
        ];

        //fetch allowed nev items from db?
        return $this->render('landing/index.html.twig', [
            'navItems' => $navItems,
            'blogForm' => $blogForm->createView()
        ]);
    }
}
