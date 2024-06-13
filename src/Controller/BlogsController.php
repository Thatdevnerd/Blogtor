<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\User;
use App\Form\BlogPostFormType;
use App\Services\BlogPostFetchService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
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

    private BlogPostFetchService $postFetchService;
    private array $blogPosts = [];

    public function __construct(HttpClientInterface $httpClient) {
        $this->postFetchService = new BlogPostFetchService($httpClient);
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/blog/posts', name: 'app_blog_posts')]
    public function index(Request $request, EntityManagerInterface $em, UserInterface $user, LoggerInterface $logger): Response
    {
        if ($user instanceof User) {
            return $this->render('blog/index.html.twig', [
                'user' => $user->getEmail()
            ]);
        }
        return new JsonResponse([
            'error' => 'Unauthorized access'
        ]);
    }


    /**
     * @throws Exception|TransportExceptionInterface
     */
    #[Route('/blog/list', name: 'app_blogs')]
    public function list(): Response
    {
        $this->blogPosts = $this->postFetchService->fetchBlogPosts();
        return $this->render('blog/index.html.twig');
    }

    /*
     * API Endpoints
     */

    #[Route('/blog/posts')]
    function getAllBlogPosts(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $blogPosts = $em->getRepository(Blogs::class)->findAll();
        $filteredBlogPosts = array_filter($blogPosts, function($item) {
            $item->setDate(new \DateTime(null));
            return $item;
        });
        return new JsonResponse($filteredBlogPosts);
    }

    #[Route('/blog/post/{id}')]
    function getBlogPosts(Request $request, EntityManagerInterface $em): JsonResponse
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
            return new JsonResponse([
                'message' => 'nothing found'
            ]);
        }
    }
}
