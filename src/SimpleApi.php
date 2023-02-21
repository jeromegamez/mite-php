<?php

declare(strict_types=1);

namespace Gamez\Mite;

use Beste\Json;
use Gamez\Mite\Api\ApiClient;

final class SimpleApi
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
     * @return array<non-empty-string, mixed>
     */
    public function getAccount(): array
    {
        return $this->getSingle('account');
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    public function getMyself(): array
    {
        return $this->getSingle('myself');
    }

    /**
     * @param array<non-empty-string, mixed> $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getActiveCustomers(?array $params = null): array
    {
        return $this->get('customers', 'customer', $params);
    }

    /**
     * @param array<non-empty-string, mixed> $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getArchivedCustomers(?array $params = null): array
    {
        return $this->get('customers/archived', 'customer', $params);
    }

    /**
     * @param positive-int|numeric-string $id
     *
     * @return array<non-empty-string, mixed>
     */
    public function getCustomer(int|string $id): array
    {
        return $this->getSingle("customers/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed> $data
     *
     * @return array<non-empty-string, mixed>
     */
    public function createCustomer(array $data): array
    {
        $response = $this->client->post('customers', ['customer' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param positive-int|numeric-string $id
     * @param array<non-empty-string, mixed> $data
     *
     * @return array<non-empty-string, mixed>
     */
    public function updateCustomer(int|string $id, array $data): array
    {
        $this->client->patch("customers/{$id}", ['customer' => $data]);

        return $this->getCustomer($id);
    }

    /**
     * @param positive-int|numeric-string $id
     */
    public function deleteCustomer(int|string $id): void
    {
        $this->client->delete("customers/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed>|null $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getActiveProjects(?array $params = null): array
    {
        return $this->get('projects', 'project', $params);
    }

    /**
     * @param array<non-empty-string, mixed>|null $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getArchivedProjects(?array $params = null): array
    {
        return $this->get('projects/archived', 'project', $params);
    }

    /**
     * @param positive-int|numeric-string $id
     *
     * @return array<non-empty-string, mixed>
     */
    public function getProject(int|string $id): array
    {
        return $this->getSingle("projects/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed> $data
     *
     * @return array<non-empty-string, mixed>
     */
    public function createProject(array $data): array
    {
        $response = $this->client->post('projects', ['project' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param positive-int|numeric-string $id
     * @param array<non-empty-string, mixed> $data
     *
     * @return array<non-empty-string, mixed>
     */
    public function updateProject(int|string $id, array $data): array
    {
        $this->client->patch("projects/{$id}", ['project' => $data]);

        return $this->getProject($id);
    }

    /**
     * @param positive-int|numeric-string $id
     */
    public function deleteProject(int|string $id): void
    {
        $this->client->delete("projects/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed>|null $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getActiveServices(?array $params = null): array
    {
        return $this->get('services', 'service', $params);
    }

    /**
     * @param array<non-empty-string, mixed> $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getArchivedServices(?array $params = null): array
    {
        return $this->get('services/archived', 'service', $params);
    }

    /**
     * @param positive-int|numeric-string $id
     *
     * @return array<non-empty-string, mixed>
     */
    public function getService(int|string $id): array
    {
        return $this->getSingle("services/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed> $data
     *
     * @return array<non-empty-string, mixed>
     */
    public function createService(array $data): array
    {
        $response = $this->client->post('services', ['service' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param positive-int|numeric-string $id
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updateService(int|string $id, array $data): array
    {
        $this->client->patch("services/{$id}", ['service' => $data]);

        return $this->getService($id);
    }

    /**
     * @param positive-int|numeric-string $id
     */
    public function deleteService(int|string $id): void
    {
        $this->client->delete("services/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed> $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getTimeEntries(?array $params = null): array
    {
        return $this->get('time_entries', 'time_entry', $params);
    }

    /**
     * @param string|string[] $groupBy
     * @param array<non-empty-string, mixed>|null $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getGroupedTimeEntries(array|string $groupBy, ?array $params = null): array
    {
        if (is_array($groupBy)) {
            $groupBy = implode(',', $groupBy);
        }

        $params['group_by'] = $groupBy;

        return $this->get('time_entries', 'time_entry_group', $params);
    }

    /**
     * @param positive-int|numeric-string $id
     *
     * @return array<non-empty-string, mixed>
     */
    public function getTimeEntry(int|string $id): array
    {
        return $this->getSingle("time_entries/{$id}");
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function createTimeEntry(array $data): array
    {
        $response = $this->client->post('time_entries', ['time_entry' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param positive-int|numeric-string $id
     * @param array<non-empty-string, mixed> $data
     *
     * @return array<non-empty-string, mixed>
     */
    public function updateTimeEntry(int|string $id, array $data): array
    {
        $this->client->patch("time_entries/{$id}", ['time_entry' => $data]);

        return $this->getTimeEntry($id);
    }

    /**
     * @param int|numeric-string $id
     */
    public function deleteTimeEntry(int|string $id): void
    {
        $this->client->delete("time_entries/{$id}");
    }

    /**
     * @param array<non-empty-string, mixed> $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getActiveUsers(?array $params = null): array
    {
        return $this->get('users', 'user', $params);
    }

    /**
     * @param array<non-empty-string, mixed> $params
     *
     * @return array<non-empty-string, mixed>
     */
    public function getArchivedUsers(?array $params = null): array
    {
        return $this->get('users/archived', 'user', $params);
    }

    /**
     * @param non-empty-string $endpoint
     * @param array<non-empty-string, mixed>|null $params
     *
     * @return array<non-empty-string, mixed>
     */
    private function get(string $endpoint, string $column, ?array $params = null): array
    {
        $response = $this->client->get($endpoint, $params);

        $data = JSON::decode((string) $response->getBody(), true);

        /** @var array<non-empty-string, mixed> $result */
        $result = array_column($data, $column);

        return $result;
    }

    /**
     * @param non-empty-string $endpoint
     *
     * @return array<non-empty-string, mixed>
     */
    private function getSingle(string $endpoint): array
    {
        $response = $this->client->get($endpoint);
        /** @var array<non-empty-string|mixed> $data */
        $data = JSON::decode((string) $response->getBody(), true);

        /** @var array<non-empty-string|mixed> $data */
        $current = current($data);

        return $current;
    }
}
