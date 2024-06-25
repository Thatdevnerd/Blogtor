<?php

namespace App\Controller;

use App\Entity\Blogs;
use App\Form\BlogsType;
use App\Repository\BlogsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminCrudController extends AbstractController
{

    #[Route('/', name: 'app_admin_crud_index', methods: ['GET'])]
    public function index(): RedirectResponse {
        return $this->redirectToRoute('app_admin_crud_overview', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/crud', name: 'app_admin_crud_overview', methods: ['GET'])]
    public function crud(BlogsRepository $blogsRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'blogs' => $blogsRepository->findAll() ? $blogsRepository->findAll() : "no blogs found",
        ]);
    }

    #[Route('/new', name: 'app_admin_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blogs();
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_crud/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_crud_show', methods: ['GET'])]
    public function show(Blogs $blog): Response
    {
        return $this->render('admin/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blogs $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogsType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/edit.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Blogs $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
