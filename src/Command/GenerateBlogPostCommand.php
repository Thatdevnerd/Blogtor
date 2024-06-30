<?php

namespace App\Command;

use App\Entity\Blogs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'generate:blog-post',
    description: 'Generate a new blog post.'
)]
class GenerateBlogPostCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'The title of the blog post.')
            ->addArgument('content', InputArgument::REQUIRED, 'The content of the blog post.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $blog = new Blogs();

        $title = $input->getArgument('title');
        $content = $input->getArgument('content');

        if (!$title || !$content) {
            $io->error('Title and content are required.');
            return Command::FAILURE;
        }

        $blog->setTitle($title);
        $blog->setContent($content);
        $blog->setDate(new \DateTime());

        $this->em->persist($blog);
        $this->em->flush();

        $io->success('The blog post has been generated');

        return Command::SUCCESS;
    }
}
