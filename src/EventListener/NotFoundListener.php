<?php

namespace App\EventListener;

use http\Env\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final readonly class NotFoundListener
{
    private LoggerInterface $logger;
    private Environment $twig;

    public function __construct(
        LoggerInterface $logger,
        Environment $twig
    ) {
        $this->logger = $logger;
        $this->twig = $twig;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 1)]
    public function onKernelTerminate(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof NotFoundHttpException) {
            $message = sprintf(
                'Not found exception thrown with message: %s %s',
                $exception->getMessage(),
                $exception->getCode()
            );

            $event->setResponse(new \Symfony\Component\HttpFoundation\Response(
                $this->twig->render('errors/404.html.twig', [
                    'message' => $message,
                ]), 404)
            );
        }
    }
}
