<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiError;
use Gamez\Mite\Exception\InvalidArgument;
use Gamez\Mite\Mite;
use Psr\Http\Message\ResponseInterface;

interface ApiClient
{
    public const USER_AGENT = 'gamez/mite/'.Mite::VERSION.' (https://github.com/jeromegamez/mite-php))';

    /**
     * Perform a GET request to an URI with optional query parameters.
     *
     * @param string $endpoint
     * @param array|null $params
     *
     * @throws ApiError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function get(string $endpoint, array $params = null): ResponseInterface;

    /**
     * @param string $endpoint
     * @param array|null $data
     *
     * @throws ApiError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function post(string $endpoint, array $data = null): ResponseInterface;

    /**
     * @param string $endpoint
     * @param array|null $data
     *
     * @throws ApiError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function patch(string $endpoint, array $data = null): ResponseInterface;

    /**
     * @param string $endpoint
     *
     * @throws ApiError
     * @throws InvalidArgument
     *
     * @return ResponseInterface
     */
    public function delete(string $endpoint): ResponseInterface;
}
