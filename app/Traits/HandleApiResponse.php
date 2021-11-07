<?php

namespace Bowhead\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

trait HandleApiResponse
{

    /**
     * Send a 200 success response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function okResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(true, $message, $data);

        return response()->json($data, 200);
    }

    /**
     * Send a 200 success response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function createdResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(true, $message, $data);

        return response()->json($data, 201);
    }

    /**
     * Format the payload for the response.
     * @param bool $status
     * @param string $message
     * @param null $data
     * @return array
     */
    public function prepareApiResponse(bool $status, string $message, $data = null): array
    {
        $responseData = ['status' => $status, 'message' => $message];

        if ($data) {
            $responseData['data'] = $data;
        }

        return $responseData;
    }

    /**
     * Send a 400 Bad request response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function errorResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 400);
    }

    /**
     * Send a 404 Not found response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function notFoundResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 404);
    }

    /**
     * Send a 400 Bad request response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function badRequestResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 400);
    }

    /**
     * Send a 401 unauthorized response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function UnauthorizedResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 401);
    }

    /**
     * Send a 403 Forbidden response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function ForbiddenResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 403);
    }

    /**
     * Send a 422 Validation error response.
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function validationErrorResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 422);
    }

    /**
     * Send a 500 Server error response
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function ServerErrorResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 500);
    }

    /**
     * Send a 503 Service unavailable response
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function ServiceUnavailableResponse(string $message, $data = null): JsonResponse
    {
        $data = $this->prepareApiResponse(false, $message, $data);

        return response()->json($data, 503);
    }

    /**
     * Send a response using a statusCode
     * @param int $statusCode
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function sendResponse(int $statusCode, string $message, $data = null): JsonResponse
    {
        $responseData = $this->prepareApiResponse(false, $message, $data);

        return response()->json($responseData, $statusCode);
    }
}
