<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class NotFoundListener
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

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

            $event->setResponse(new JsonResponse([
                'info' => 'hit not found event',
                'message' => $message
            ], 404));
        }
    }
}
