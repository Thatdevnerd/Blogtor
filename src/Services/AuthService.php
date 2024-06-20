<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class AuthService
{

    private UserPasswordHasherInterface $hasher;
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    )
    {
        $this->em = $em;
        $this->hasher = $hasher;
    }

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