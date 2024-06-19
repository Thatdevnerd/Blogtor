<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    private bool $emailIsValid = false;

    #[Route('/register', name: 'app_register', methods: ['GET'])]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->emailIsValid = $this->validateEmail($form->get('emil')->getData());
            if ($this->emailIsValid) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_login');
            }

            //TODO Get the email_error to display
            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form,
                'email_error' => $this->emailIsValid ? null : 'Invalid email address'
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form
        ]);
    }

    //TODO Replace this with symfony built in email validation
    private function validateEmail(String $email): bool {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $pattern = '/\.[a-zA-Z]{2,}$/';
        if (!preg_match($pattern, $email)) {
            return false;
        }

        return true;
    }
}
