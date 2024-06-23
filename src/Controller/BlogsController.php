<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Entity\User;
use App\Form\BlogsFormType;
use App\Services\BlogService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BlogsController extends AbstractController
{
    private EntityManagerInterface $em;
    private ValidatorInterface $validator;
    private BlogService $blogService;

    public function __construct(EntityManagerInterface $em,
                                ValidatorInterface     $validator,
                                BlogService            $blogService,
    )
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->blogService = $blogService;
    }


    #[Route('/blog/posts', name: 'app_blog_posts', methods: ['GET'])]
    public function index(UserInterface $user): Response
    {
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('blog_overview/overview/index.html.twig', [
            'user_email' => $user->getEmail(),
            'posts' => $this->blogService->fetchPost(true)
        ]);
    }

    #[Route('/blog/add', name: 'app_blog_add', methods: ['GET', 'POST'])]
    public function addBlog(Request $request, Blogs $blogs): RedirectResponse | Response {
        $form = $this->createForm(BlogsFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->blogService->createPost($form, $blogs);
            return $this->redirectToRoute('app_blog_posts');
        }

        return $this->render('blog_overview/create/create-blog.html.twig', [
            'form' => $this->createForm(BlogsFormType::class)->createView()
        ]);
    }
}
