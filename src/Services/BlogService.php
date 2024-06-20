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
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BlogService {

    private EntityManagerInterface $em;
    private readonly ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em,
                                ValidatorInterface $validator,
    )
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param bool $all
     * @param int|null $id
     * @return array|Blogs[]|object[]
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function fetchPost(bool $all = false, int $id = null): array
    {
        if (!$all) {
            return $this->em->getRepository(Blogs::class)->find($id);
        } else {
            return $this->em->getRepository(Blogs::class)->findAll();
        }
    }

    /**
     * @param Request $request
     * @param Blogs $blog
     *
     * @return void
     */
    public function createPost(Request $request, Blogs $blog): void
    {
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

    /**
     * @param Blogs $blog
     * @return void
     */
    private function validateDTO(Blogs $blog): void
    {
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