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
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class BlogsController extends AbstractController
{
    private readonly BlogPostFetchService $blogPostFetchService;
    private EntityManagerInterface $em;

    public function __construct(BlogPostFetchService $blogPostFetchService, EntityManagerInterface $em)
    {
        $this->blogPostFetchService = $blogPostFetchService;
        $this->em = $em;
    }


    #[Route('/blog/posts', name: 'app_blog_posts', methods: ['GET'])]
    public function index(UserInterface $user): Response
    {
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('blog_overview/index.html.twig', [
            'user_email' => $user->getEmail(),
            'posts' => $this->blogPostFetchService->fetchPost(true)
        ]);
    }

    #[Route('/blog/create', name: 'app_blog_create', methods: ['GET'])]
    public function blogCreate() {
        throw new NotImplementedException('This method is not implemented');
    }

    #[Route('/blog/post/{id}', name: 'app_blog_post', methods: ['GET'])]
    function post(Request $request): JsonResponse
    {
        $id = $request->get('id');
        $blogPost = $this->em->getRepository(Blogs::class)->find($id);
        if ($blogPost) {
            return new JsonResponse([
                'title' => $blogPost->getTitle(),
                'content' => $blogPost->getContent(),
                'date' => $blogPost->getDate()->getTimestamp()
            ]);
        }
        return $this->json([
            'message' => 'nothing found'
        ], Response::HTTP_NOT_FOUND);
    }

    #[Route('/blog/posts/all', name: 'app_blog_post_all', methods: ['GET'])]
    function posts(): JsonResponse
    {
        $blogPosts = $this->em->getRepository(Blogs::class)->findAll();

        if (empty($blogPosts)) {
            return $this->json([
                'message' => 'nothing found'
            ], Response::HTTP_NOT_FOUND);
        }

        $posts = array_map([
            $this, 'transformBlogPosts'
        ], $blogPosts);

        return $this->json($posts);
    }

    private function transformBlogPosts(Blogs $blogPost): array {
        return [
            'title' => $blogPost->getTitle(),
            'content' => $blogPost->getContent(),
            'date' => $blogPost->getDate()->getTimestamp()
        ];
    }
}
