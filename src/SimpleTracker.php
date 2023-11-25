<?php

declare(strict_types=1);

namespace Gamez\Mite;

use Gamez\Mite\Api\ApiClient;

final class SimpleTracker
{
    private function __construct(
        private readonly ApiClient $client,
    ) {
    }

    public static function withApiClient(ApiClient $apiClient): self
    {
        return new self($apiClient);
    }

    /**
     * @return array<string, mixed>
     */
    public function status(): array
    {
        $response = $this->client->get('tracker');
        $data = json_decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function start(int|string $id): array
    {
        $response = $this->client->patch("tracker/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function stop(int|string $id): array
    {
        $response = $this->client->delete("tracker/{$id}");
        $data = json_decode((string) $response->getBody(), true);

        return current($data);
    }
}
