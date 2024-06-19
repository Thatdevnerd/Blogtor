<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\User;
use App\Services\BlogPostFetchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BlogsController extends AbstractController
{
    private BlogPostFetchService $blogPostFetchService;

    public function __construct(HttpClientInterface $httpClient, BlogPostFetchService $blogPostFetchService) {
        $this->blogPostFetchService = $blogPostFetchService;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/blog/posts', name: 'app_blog_posts', methods: ['GET'])]
    public function index(Request $request,
                          BlogPostFetchService $postFetchService,
                          EntityManagerInterface $em,
                          UserInterface $user): Response
    {
        if (!$user instanceof User) { return $this->redirectToRoute('app_login'); }
        return $this->render('blog_overview/index.html.twig', [
            'user_email' => $user->getEmail(),
            'posts' => $this->blogPostFetchService->fetchPost(true)
        ]);
    }


    #[Route('/blog/post/{id}', name: 'app_blog_post', methods: ['GET'])]
    function post(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id = $request->get('id');
        $blogPost = $em->getRepository(Blogs::class)->find($id);
        if ($blogPost) {
            return new JsonResponse([
                'title' => $blogPost->getTitle(),
                'content' => $blogPost->getContent(),
                'date' => $blogPost->getDate()->getTimestamp()
            ]);
        } else {
            return $this->json([
                'message' => 'nothing found'
            ]);
        }
    }

    #[Route('/blog/posts/all', name: 'app_blog_post_all', methods: ['GET'])]
    function posts(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $blogPosts = $em->getRepository(Blogs::class)->findAll();
        $posts = [];
        foreach ($blogPosts as $blogPost) {
            $posts[] = [
                'title' => $blogPost->getTitle(),
                'content' => $blogPost->getContent(),
                'date' => $blogPost->getDate()->getTimestamp()
            ];
        }
        if (empty($posts)) {
            return $this->json([
                'message' => 'nothing found'
            ]);
        }
        return $this->json($posts);
    }
}
