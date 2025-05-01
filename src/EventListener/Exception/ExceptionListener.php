<?php

namespace App\EventListener\Exception;

use App\Exception\EpisodeNotFoundException;
use App\Exception\ReviewCreationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

#[AsEventListener]
readonly class ExceptionListener
{
    public function __construct(
        private LoggerInterface $logger,
    )
    {
    }

    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage();
        } elseif ($exception instanceof EpisodeNotFoundException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $message = $exception->getMessage();
        } elseif ($exception instanceof ReviewCreationException) {
            $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $message = $exception->getMessage();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $message = 'Unexpected error: ' . $exception->getMessage();
        }

        $responseContent = [
            'error' => [
                'code' => $statusCode,
                'message' => $message
            ],
        ];

        $response = new JsonResponse($responseContent, $statusCode);
        $event->setResponse($response);

        if ($statusCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $this->logger->error('An error occurred', ['exception' => $exception]);
        } elseif ($statusCode >= Response::HTTP_BAD_REQUEST and $statusCode !== Response::HTTP_NOT_FOUND) {
            $this->logger->debug('An error occurred', ['exception' => $exception]);
        }
    }
}
