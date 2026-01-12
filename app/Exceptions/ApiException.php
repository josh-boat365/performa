<?php

namespace App\Exceptions;

use Exception;

/**
 * ApiException
 *
 * Custom exception for API-related errors. Provides structured error handling
 * for external API calls with status code and detailed error information.
 */
class ApiException extends Exception
{
    /**
     * The HTTP status code from the API response
     */
    protected int $statusCode;

    /**
     * The detailed error message from the API
     */
    protected string $details;

    /**
     * Create a new ApiException instance
     *
     * @param string $message The user-friendly error message
     * @param int $statusCode The HTTP status code
     * @param string $details The detailed error information
     */
    public function __construct(
        string $message = 'An API error occurred',
        int $statusCode = 500,
        string $details = ''
    ) {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    /**
     * Get the HTTP status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get the detailed error information
     */
    public function getDetails(): string
    {
        return $this->details;
    }

    /**
     * Check if this is a client error (4xx status code)
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * Check if this is a server error (5xx status code)
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }

    /**
     * Check if this is a connection error
     */
    public function isConnectionError(): bool
    {
        return $this->statusCode === 0;
    }
}
