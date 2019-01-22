<?php

declare(strict_types=1);

namespace Gamez\Mite;

use Gamez\Mite\Api\ApiClient;
use Gamez\Mite\Support\JSON;

final class SimpleTracker
{
    /**
     * @var ApiClient
     */
    private $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function status(): array
    {
        $response = $this->client->get('tracker');
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    public function start($id): array
    {
        $response = $this->client->patch("tracker/{$id}");
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    public function stop($id): array
    {
        $response = $this->client->delete("tracker/{$id}");
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }
}
