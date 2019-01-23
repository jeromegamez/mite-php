<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiClientError;
use Gamez\Mite\Exception\InvalidArgument;
use Psr\Http\Message\ResponseInterface;

interface ApiClient
{
    public const USER_AGENT = 'gamez/mite (https://github.com/jeromegamez/mite-php))';

    /**
     * Perform a HEAD request to an endpoint with optional query parameters.
     *
     * @param string $endpoint
     * @param array|null $params
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function head(string $endpoint, array $params = null): ResponseInterface;

    /**
     * Perform a GET request to an endpoint with optional query parameters.
     *
     * @param string $endpoint
     * @param array|null $params
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function get(string $endpoint, array $params = null): ResponseInterface;

    /**
     * Perform a POST request to an endpoint with optional data.
     *
     * @param string $endpoint
     * @param array|null $data
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function post(string $endpoint, array $data = null): ResponseInterface;

    /**
     * Perform a PATCH request to an endpoint with optional data.
     *
     * @param string $endpoint
     * @param array|null $data
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function patch(string $endpoint, array $data = null): ResponseInterface;

    /**
     * Perform a DELETE request to an endpoint with optional query parameters.
     *
     * @param string $endpoint
     * @param array|null $params
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function delete(string $endpoint, array $params = null): ResponseInterface;
}
