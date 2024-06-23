<?php
namespace App\Services;

use App\DTO\BlogDTO;
use App\Entity\Blogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

class BlogService {

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ValidatorInterface
     */
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
        }
        return $this->em->getRepository(Blogs::class)->findAll();
    }

    /**
     * @param Request $request
     * @param Blogs $blog
     *
     * @return void
     */
    public function createPost(FormInterface $form, Blogs $blog): void
    {
        $title = $form->get('title')->getData();
        $content = $form->get('content')->getData();
        $date = $form->get('date')->getData();

        $this->validateDTO($form);

        $blog->setTitle($title);
        $blog->setContent($content);
        $blog->setDate($date);

        $this->em->persist($blog);
        $this->em->flush();
    }

    /**
     * @param FormInterface $form
     * @return void
     */
    private function validateDTO(FormInterface $form): void
    {
        $dto = new BlogDTO(
            $form->get('title')->getData(),
            $form->get('content')->getData(),
            $form->get('date')->getData()
        );
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new \InvalidArgumentException('Invalid data');
        }
    }
}