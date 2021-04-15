<?php

declare(strict_types=1);

namespace Gamez\Mite;

use Gamez\Mite\Api\ApiClient;
use Gamez\Mite\Support\JSON;

final class SimpleTracker
{
    private ApiClient $client;

    private function __construct()
    {
    }

    public static function withApiClient(ApiClient $apiClient): self
    {
        $that = new self();
        $that->client = $apiClient;

        return $that;
    }

    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        $response = $this->client->get('tracker');
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function start($id): array
    {
        $response = $this->client->patch("tracker/{$id}");
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function stop($id): array
    {
        $response = $this->client->delete("tracker/{$id}");
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }
}
