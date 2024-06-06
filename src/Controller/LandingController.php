<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Form\BlogPostFormType;
use App\Services\BlogPostFetchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LandingController extends AbstractController
{
    private BlogPostFetchService $postFetchService;

    public function __construct(HttpClientInterface $client, BlogPostFetchService $postFetchService) {
        $this->postFetchService = $postFetchService;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/blog/post', name: 'app_landing')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $blogForm = $this->createForm(BlogPostFormType::class);
        $blogForm->handleRequest($request);

        if ($blogForm->isSubmitted() && $blogForm->isValid()) {
            $blogEntity = new Blogs();

            $blogEntity->setTitle($blogForm->get('title')->getData());
            $blogEntity->setContent($blogForm->get('content')->getData());
            $blogEntity->setDate($blogForm->get('date')->getData());

            $em->persist($blogEntity);
            $em->flush();

            return new JsonResponse(['message' => 'Blog post created!', 'data' => [
                'title' => $blogEntity->getTitle(),
                'content' => $blogEntity->getContent(),
                'date' => $blogEntity->getDate()
            ]], 200);
        }

        $navItems = [
            ['name' => 'Home', 'url' => '/', 'allowed' => 0],
            ['name' => 'Post', 'url' => '/post', 'allowed' => 0]
        ];

        $blogPost = $this->postFetchService->fetchBlogPost();

        if (!is_object($blogPost)) {
            $blogPost = ['error' => 'failed to get data from blogs'];
        }

        return $this->render('landing/index.html.twig', [
            'navItems' => $navItems,
            'blogForm' => $blogForm->createView(),
            'blog_posts' => $blogPost
        ]);
    }

    #[Route('/blog/post/{id}', name: 'app_blog_posts')]
    function getBlogPosts(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id = $request->get('id');
        if (is_nan($id)) {
            return new JsonResponse([
                'error' => "ID isn't numeric"
            ]);
        }

        $blogPost = $em->getRepository(Blogs::class)->find($request->get('id'));
        if ($blogPost) {
            return new JsonResponse([
                'title' => $blogPost->getTitle(),
                'content' => $blogPost->getContent(),
                'date' => $blogPost->getDate()->getTimestamp()
            ]);
        } else {
            return new JsonResponse([
               'message' => 'nothing found'
           ]);
       }
    }
}
