<?php
namespace App\Services;

use App\DTO\BlogDTO;
use App\Entity\Blogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @return array
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function fetchPost(bool $all = false, int $id = null): array {
        if (!$all) {
            if (is_null($id)) { return ['error' => 'id is null']; }
            $response = $this->http->request('GET', 'http://localhost:8000/blog/posts/' . $id, [
                'verify_peer' => false
            ]);
        } else {
            $response = $this->http->request('GET', 'http://localhost:8000/blog/posts/all', [
                'verify_peer' => false
            ]);
        }
        return json_decode($response->getContent(), true);
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
}