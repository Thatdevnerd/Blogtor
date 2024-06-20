<?php
namespace App\Services;

use App\DTO\BlogDTO;
use App\Entity\Blogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BlogService {
    private EntityManagerInterface $em;
    private readonly ValidatorInterface $validator;
    private readonly HttpClientInterface $http;

    public function __construct(EntityManagerInterface $em,
                                ValidatorInterface $validator,
                                HttpClientInterface $http
    ) {
        $this->em = $em;
        $this->validator = $validator;
        $this->http = $http;
    }

    /**
     * @param bool $all
     * @param int|null $id
     * @return JsonResponse
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function fetchPost(bool $all = false, int $id = null): JsonResponse
    {
        if (!$all) {
            $post = $this->em->getRepository(Blogs::class)->find($id);
            return new JsonResponse(array_map([$this, 'transformBlogPosts'], $post));
        } else {
            $posts = $this->em->getRepository(Blogs::class)->findAll();
            return new JsonResponse(array_map([$this, 'transformBlogPosts'], $posts));
        }
    }

    public function createPost(Request $request, Blogs $blog) {
        [
            'title' => $title,
            'content' => $content,
        ] = $request->request->all();

        $this->validateDTO($blog);

        $blog->setTitle($title);
        $blog->setContent($content);

        $this->em->persist($blog);
        $this->em->flush();
    }

    private function validateDTO(Blogs $blog) {
        $dto = new BlogDTO(
            $blog->getTitle(),
            $blog->getContent(),
            $blog->getDate()->getTimestamp()
        );
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid data');
        }
    }

    private function transformBlogPosts(Blogs $blogPost): array {
        return [
            'title' => $blogPost->getTitle(),
            'content' => $blogPost->getContent(),
            'date' => $blogPost->getDate()->getTimestamp()
        ];
    }
}