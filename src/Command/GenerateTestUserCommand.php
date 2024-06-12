<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'generate:test-user',
    description: 'Generate a test user for the application',
)]
class GenerateTestUserCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email (username) for the user')
            ->addArgument('password', InputArgument::REQUIRED, 'Password for the test user')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('email');
        $password = $input->getArgument('password');

        if (!$username && !$password) { return Command::FAILURE; }

        //TODO Generate user

        $io->success('User generated successfully');

        return Command::SUCCESS;
    }
}
