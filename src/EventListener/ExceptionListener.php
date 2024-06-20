<?php

namespace App\EventListener;

use App\Exceptions\BlogNotFoundException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

final readonly class ExceptionListener
{
    private Environment $twig;

    public function __construct(
        Environment $twig
    ) {
        $this->twig = $twig;
    }

    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof BlogNotFoundException) {
            $event->setResponse(new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND));
        }

        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(new Response(
                $this->twig->render('errors/404.html.twig', ['message' => 'Blog not found']),
            ));
        }
    }
}
