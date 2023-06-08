<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiClientError;
use Gamez\Mite\Exception\InvalidArgument;
use Psr\Http\Message\ResponseInterface;

interface ApiClient
{
    public const USER_AGENT = 'gamez/mite (https://github.com/jeromegamez/mite-php)';

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $params
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     */
    public function head(string $endpoint, ?array $params = null): ResponseInterface;

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $params
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     */
    public function get(string $endpoint, ?array $params = null): ResponseInterface;

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $data
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     */
    public function post(string $endpoint, ?array $data = null): ResponseInterface;

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $data
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     */
    public function patch(string $endpoint, ?array $data = null): ResponseInterface;

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $params
     *
     * @throws ApiClientError
     * @throws InvalidArgument
     */
    public function delete(string $endpoint, ?array $params = null): ResponseInterface;
}
