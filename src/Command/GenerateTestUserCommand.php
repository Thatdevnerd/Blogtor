<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'generate:test-admin',
    description: 'Generate a test admin account for the application',
)]
class GenerateTestUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email (username) for the user')
                ->addOption("rank", null, InputArgument::OPTIONAL, "Rank of the user", "admin")
            ->addArgument('password', InputArgument::REQUIRED, 'Password for the test user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('email');
        $password = $this->passwordHasher->hashPassword(
            $user,
            $input->getArgument('password')
        );
        $role = $input->getOption("rank");

        $roles = [
            'ROLE_ADMIN',
            'ROLE_USER'
        ];

        if (!$username && !$password) {
            $io->error('Username and password are required');
            return Command::FAILURE;
        }

        if (in_array($role, $roles)) {
            $user->setRoles([$role]);
        } else {
            $io->error('Invalid rank');
            return Command::FAILURE;
        }

        $user->setEmail($username);
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Admin account generated successfully');
        return Command::SUCCESS;
    }
}
