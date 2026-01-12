<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ApiException;

/**
 * BaseApiService
 *
 * Abstract base class for all API service classes. Provides common methods
 * for making HTTP requests to external APIs with error handling, logging,
 * and retry logic.
 */
abstract class BaseApiService
{
    /**
     * The base URL for the API
     */
    protected string $baseUrl;

    /**
     * The timeout for API requests in seconds
     */
    protected int $timeout;

    /**
     * Whether to verify SSL certificates
     */
    protected bool $verifySsl;

    /**
     * The access token for authenticated requests
     */
    protected ?string $accessToken = null;

    /**
     * Service name for logging purposes
     */
    abstract protected function getServiceName(): string;

    /**
     * Get the configuration key for this service
     */
    abstract protected function getConfigKey(): string;

    /**
     * Initialize the service with configuration
     */
    protected function initialize(): void
    {
        $configKey = $this->getConfigKey();
        $this->baseUrl = config("api.{$configKey}.base_url");
        $this->timeout = config("api.{$configKey}.timeout", 30);
        // Default to false for development, matching old implementation behavior
        $this->verifySsl = config('api.verify_ssl', false);
    }

    /**
     * Set the access token for authenticated requests
     */
    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;
        return $this;
    }

    /**
     * Make a GET request to the API
     *
     * @param string $endpoint The API endpoint (e.g., '/Kpi')
     * @param array $params Query parameters
     * @return array The response data
     * @throws ApiException
     */
    protected function get(string $endpoint, array $params = []): array
    {
        return $this->request('GET', $endpoint, $params);
    }

    /**
     * Make a POST request to the API
     *
     * @param string $endpoint The API endpoint
     * @param array $data Request payload
     * @return array The response data
     * @throws ApiException
     */
    protected function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Make a PUT request to the API
     *
     * @param string $endpoint The API endpoint
     * @param array $data Request payload
     * @return array The response data
     * @throws ApiException
     */
    protected function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, $data);
    }

    /**
     * Make a DELETE request to the API
     *
     * @param string $endpoint The API endpoint
     * @param array $data Request payload
     * @return array The response data
     * @throws ApiException
     */
    protected function delete(string $endpoint, array $data = []): array
    {
        return $this->request('DELETE', $endpoint, $data);
    }

    /**
     * Make an HTTP request to the API with error handling and logging
     *
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $payload Request payload (query params for GET, body for POST/PUT)
     * @return array The response data
     * @throws ApiException
     */
    protected function request(string $method, string $endpoint, array $payload = []): array
    {
        if (!$this->baseUrl) {
            $this->initialize();
        }

        // Lazy-load the access token from session if not already set
        if (!$this->accessToken && session('api_token')) {
            $this->accessToken = session('api_token');
        }

        $url = $this->buildUrl($endpoint);

        try {
            $request = Http::timeout($this->timeout);

            // Handle SSL verification
            if (!$this->verifySsl) {
                $request = $request->withoutVerifying();
            }

            // Add authentication token if available
            if ($this->accessToken) {
                $request = $request->withToken($this->accessToken);
            }

            // Execute the request based on method
            $response = match ($method) {
                'GET' => $request->get($url, $payload),
                'POST' => $request->post($url, $payload),
                'PUT' => $request->put($url, $payload),
                'DELETE' => $request->delete($url, $payload),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            // Check for successful response
            if (!$response->successful()) {
                $this->logError($method, $url, $response, $payload);

                // Extract meaningful error message from response
                $errorMessage = $this->extractErrorMessage($response);

                throw new ApiException(
                    $errorMessage,
                    $response->status(),
                    $response->body()
                );
            }

            return $response->json() ?? [];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Connection error calling {$this->getServiceName()}", [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new ApiException(
                "Unable to connect to {$this->getServiceName()}. Please check your internet connection.",
                0,
                $e->getMessage()
            );
        } catch (ApiException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Unexpected error in {$this->getServiceName()}", [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new ApiException(
                "An unexpected error occurred while communicating with {$this->getServiceName()}.",
                500,
                $e->getMessage()
            );
        }
    }

    /**
     * Extract a meaningful error message from the API response
     */
    protected function extractErrorMessage($response): string
    {
        try {
            $body = $response->body();

            // Try to parse as JSON first
            $data = json_decode($body, true);

            if (is_array($data)) {
                // Check common error message fields
                if (!empty($data['message'])) {
                    return $data['message'];
                }
                if (!empty($data['error'])) {
                    return $data['error'];
                }
                if (!empty($data['errors']) && is_array($data['errors'])) {
                    $firstError = reset($data['errors']);
                    if (is_string($firstError)) {
                        return $firstError;
                    }
                }
            }

            // If response is plain text, return it
            if (is_string($body) && !empty($body)) {
                return $body;
            }
        } catch (\Exception $e) {
            // Fallback to generic message
        }

        return "API request failed: {$response->status()}";
    }

    /**
     * Build the full URL from endpoint
     */
    protected function buildUrl(string $endpoint): string
    {
        if (!$this->baseUrl) {
            $this->initialize();
        }

        // Remove leading slash if present to avoid double slashes
        $endpoint = ltrim($endpoint, '/');

        return rtrim($this->baseUrl, '/') . '/' . $endpoint;
    }

    /**
     * Log API errors with detailed information
     */
    protected function logError(string $method, string $url, $response, array $payload): void
    {
        Log::error("API Error - {$this->getServiceName()}", [
            'method' => $method,
            'url' => $url,
            'status' => $response->status(),
            'payload' => $this->sanitizePayload($payload),
            'response' => $response->body(),
        ]);
    }

    /**
     * Sanitize payload for logging (remove sensitive data)
     */
    protected function sanitizePayload(array $payload): array
    {
        $sensitiveKeys = ['password', 'token', 'access_token', 'secret'];

        return array_map(function ($key, $value) use ($sensitiveKeys) {
            return in_array($key, $sensitiveKeys) ? '***' : $value;
        }, array_keys($payload), $payload);
    }

    /**
     * Get an endpoint URL from configuration
     *
     * @param string $endpointKey The endpoint key from config
     * @return string The full endpoint path
     */
    protected function getEndpoint(string $endpointKey): string
    {
        $configKey = $this->getConfigKey();
        return config("api.{$configKey}.endpoints.{$endpointKey}") ?? $endpointKey;
    }
}
