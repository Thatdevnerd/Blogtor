<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\User;
use App\Form\BlogPostFormType;
use App\Services\BlogPostFetchService;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Request $request, EntityManagerInterface $em,
                          UserInterface $user,
                          LoggerInterface $logger): Response
    {
        if ($user instanceof User) {
            return $this->render('blog_overview/index.html.twig', [
                'user_email' => $user->getEmail(),
            ]);
        }
        return $this->json([
            'error' => 'Unauthorized access'
        ]);
    }


    #[Route('/blog/post/{id}')]
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
}
