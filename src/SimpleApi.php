<?php

declare(strict_types=1);

namespace Gamez\Mite;

use Gamez\Mite\Api\ApiClient;
use Gamez\Mite\Support\JSON;

final class SimpleApi
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
    public function getAccount(): array
    {
        return $this->getSingle('account');
    }

    /**
     * @return array<string, mixed>
     */
    public function getMyself(): array
    {
        return $this->getSingle('myself');
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getActiveCustomers(?array $params = null): array
    {
        return $this->get('customers', 'customer', $params);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getArchivedCustomers(?array $params = null): array
    {
        return $this->get('customers/archived', 'customer', $params);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function getCustomer($id): array
    {
        return $this->getSingle("customers/{$id}");
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function createCustomer(array $data): array
    {
        $response = $this->client->post('customers', ['customer' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updateCustomer($id, array $data): array
    {
        $this->client->patch("customers/{$id}", ['customer' => $data]);

        return $this->getCustomer($id);
    }

    /**
     * @param int|numeric-string $id
     */
    public function deleteCustomer($id): void
    {
        $this->client->delete("customers/{$id}");
    }

    /**
     * @param array<string, mixed>|null $params
     *
     * @return array<string, mixed>
     */
    public function getActiveProjects(?array $params = null): array
    {
        return $this->get('projects', 'project', $params);
    }

    /**
     * @param array<string, mixed>|null $params
     *
     * @return array<string, mixed>
     */
    public function getArchivedProjects($params = null): array
    {
        return $this->get('projects/archived', 'project', $params);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function getProject($id): array
    {
        return $this->getSingle("projects/{$id}");
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function createProject(array $data): array
    {
        $response = $this->client->post('projects', ['project' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updateProject($id, array $data): array
    {
        $this->client->patch("projects/{$id}", ['project' => $data]);

        return $this->getProject($id);
    }

    /**
     * @param int|numeric-string $id
     */
    public function deleteProject($id): void
    {
        $this->client->delete("projects/{$id}");
    }

    /**
     * @param array<string, mixed>|null $params
     *
     * @return array<string, mixed>
     */
    public function getActiveServices(?array $params = null): array
    {
        return $this->get('services', 'service', $params);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getArchivedServices(?array $params = null): array
    {
        return $this->get('services/archived', 'service', $params);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function getService($id): array
    {
        return $this->getSingle("services/{$id}");
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function createService(array $data): array
    {
        $response = $this->client->post('services', ['service' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    /**
     * @param int|numeric-string $id
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updateService($id, array $data): array
    {
        $this->client->patch("services/{$id}", ['service' => $data]);

        return $this->getService($id);
    }

    /**
     * @param int|numeric-string $id
     */
    public function deleteService($id): void
    {
        $this->client->delete("services/{$id}");
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getTimeEntries(?array $params = null): array
    {
        return $this->get('time_entries', 'time_entry', $params);
    }

    /**
     * @param string|string[] $groupBy
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getGroupedTimeEntries($groupBy, $params = null): array
    {
        if (is_array($groupBy)) {
            $groupBy = implode(',', $groupBy);
        }

        $params['group_by'] = $groupBy;

        return $this->get('time_entries', 'time_entry_group', $params);
    }

    /**
     * @param int|numeric-string $id
     *
     * @return array<string, mixed>
     */
    public function getTimeEntry($id): array
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
     * @param int|numeric-string $id
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public function updateTimeEntry($id, array $data): array
    {
        $this->client->patch("time_entries/{$id}", ['time_entry' => $data]);

        return $this->getTimeEntry($id);
    }

    /**
     * @param int|numeric-string $id
     */
    public function deleteTimeEntry($id): void
    {
        $this->client->delete("time_entries/{$id}");
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getActiveUsers(?array $params = null): array
    {
        return $this->get('users', 'user', $params);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    public function getArchivedUsers(?array $params = null): array
    {
        return $this->get('users/archived', 'user', $params);
    }

    /**
     * @param array<string, mixed>|null $params
     *
     * @return array<string, mixed>
     */
    private function get(string $endpoint, string $column, ?array $params = null): array
    {
        $response = $this->client->get($endpoint, $params);
        $data = JSON::decode((string) $response->getBody(), true);

        return array_column($data, $column);
    }

    /**
     * @return array<string, mixed>
     */
    private function getSingle(string $endpoint): array
    {
        $response = $this->client->get($endpoint);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }
}
