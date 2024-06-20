<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{
    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $hasher;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @param EntityManagerInterface $em
     * @param UserPasswordHasherInterface $hasher
     */
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    )
    {
        $this->em = $em;
        $this->hasher = $hasher;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     */
    public function createUser(FormInterface $form, User $user): bool {
        $email = $form->get('email')->getData();
        $plainPassword = $form->get('plainPassword')->getData();

        if ($email && $plainPassword) {
            $user->setEmail($email);
            $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $this->em->persist($user);
            $this->em->flush();
        }
        return true;
    }
}