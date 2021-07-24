<?php

namespace App\EventListener;

use App\Exception\ErrorList;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionListener
{
    private string $env;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();
        $exception = $event->getThrowable();
        $response = $request->isXmlHttpRequest()
            ? $this->createApiResponse($exception)
            : $this->createBrowserResponse($exception);

        $event->setResponse($response);
    }

    private function createApiResponse(Throwable $exception): JsonResponse
    {
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        $data = [
            'type' => get_class($exception),
            'title' => $exception->getMessage(),
            'errors' => $exception instanceof ErrorList ? $exception->getErrors() : [(string)$exception],
        ];

        return new JsonResponse($data, $statusCode);
    }

    private function createBrowserResponse(Throwable $exception): Response
    {
        $response = new Response();

        if ($exception instanceof HttpExceptionInterface) {
            $response->headers->replace($exception->getHeaders());
        }

        $response->setStatusCode($exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR
        );

        $additionalInfo = $exception instanceof ErrorList ? join(', ', $exception->getErrors()) : '';

        if ($this->isDev()) {
            $additionalInfo .= <<<HTML
            <p>{$exception->getMessage()}</p>
            <pre>{$exception->getTraceAsString()}</pre>
            <hr />
            HTML;
        }

        $response->setContent(<<<HTML
        <p>Well 💩..., <i>there was an error</i>.</p>
        {$additionalInfo}
        HTML);

        return $response;
    }

    private function isDev(): bool
    {
        return $this->env === 'dev';
    }
}
