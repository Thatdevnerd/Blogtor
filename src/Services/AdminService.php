<?php

namespace App\Services;

use App\Entity\Blogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminService {

    public function __construct(
        public readonly EntityManagerInterface $em
    ) {}

    public function removeBlog(Request $request, Blogs $blog): void
    {
        $this->em->remove($blog);
        $this->em->flush();
    }

}