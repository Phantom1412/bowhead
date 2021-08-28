<?php

namespace Bowhead\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

trait HandleApiException
{
    /**
     * Render the exception as JSON
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderApiException(Request $request, Throwable $exception): JsonResponse
    {
        $responseData = $this->prepareApiExceptionData($exception);
        $payload = Arr::except($responseData, 'statusCode');
        $statusCode = $responseData['statusCode'];

        return response()->json($payload, $statusCode);
    }

    /**
     * Generate the status code, message and data for a particular exception
     * @param Throwable $exception
     * @return array
     */
    public function prepareApiExceptionData(Throwable $exception): array
    {
        $responseData['status'] = false;
        $message = $exception->getMessage();

        if ($exception instanceof NotFoundHttpException) {
            $responseData['message'] = empty($message) ? "Resource not found" : $message;
            $responseData["statusCode"] = 404;
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $responseData['message'] = $message;
            $responseData['statusCode'] = 405;
        } elseif ($exception instanceof ModelNotFoundException) {
            $responseData['message'] = "Model resource not found";
            $responseData['statusCode'] = 404;
        } elseif ($exception instanceof AuthenticationException) {
            $responseData['message'] = $message ?? "Unauthenticated";
            $responseData['statusCode'] = 401;
        } elseif ($exception instanceof ValidationException) {
            $responseData['message'] = $message;
            $responseData['errors'] = $exception->validator->errors();
            $responseData['statusCode'] = 422;
        } else {
            $responseData['message'] = ! $this->isHttpException($exception) ? "Server error" : $exception->getMessage();
            $responseData['statusCode'] = $this->isHttpException($exception) ? $exception->getCode() : 500;
            if ($debug = $this->extractExceptionData($exception)) {
                $responseData['debug'] = $debug;
            }
        }

        return $responseData;
    }

    /**
     * Extraction of the error message from the exception
     * @param Throwable $exception
     * @return array
     */
    public function extractExceptionData(Throwable $exception): array
    {
        if (config('app.debug') && ! $this->isHttpException($exception)) {
            $data = [
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ];
        } else {
            $data = [];
        }

        return $data;
    }
}
