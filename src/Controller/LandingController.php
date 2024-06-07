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

    public function __construct(HttpClientInterface $httpClient) {
        $this->postFetchService = new BlogPostFetchService($httpClient);
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

        $blogPost = $this->postFetchService->fetchBlogPost();

        return $this->render('landing/index.html.twig', [
            'blogForm' => $blogForm->createView(),
            'blog_posts' => $blogPost
        ]);
    }

    #[Route('/blog/post/{id}', name: 'app_blog_posts')]
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

    #[Route('/blog/posts')]
    function getAllBlogPosts(Request $request, EntityManagerInterface $em)
    {
        $blogPosts = $em->getRepository(Blogs::class)->findAll();
        $blogPostFiltered = array_filter($blogPosts, function($item) {
           $item->setDate(new \DateTime(null));
        });
        return new JsonResponse($blogPosts);
    }
}
