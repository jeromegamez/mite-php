<?php

declare(strict_types=1);

namespace Gamez\Mite;

use Gamez\Mite\Api\ApiClient;
use Gamez\Mite\Support\JSON;

final class SimpleApi
{
    /**
     * @var ApiClient
     */
    private $client;

    public function __construct(ApiClient $apiClient)
    {
        $this->client = $apiClient;
    }

    public function getAccount(): array
    {
        return $this->getSingle('account');
    }

    public function getMyself(): array
    {
        return $this->getSingle('myself');
    }

    public function getActiveCustomers($params = null): array
    {
        return $this->get('customers', 'customer', $params);
    }

    public function getArchivedCustomers($params = null): array
    {
        return $this->get('customers/archived', 'customer', $params);
    }

    public function getCustomer($id): array
    {
        return $this->getSingle("customers/{$id}");
    }

    public function createCustomer($data): array
    {
        $response = $this->client->post('customers', ['customer' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    public function updateCustomer($id, $data): array
    {
        $this->client->patch("customers/{$id}", ['customer' => $data]);

        return $this->getCustomer($id);
    }

    public function deleteCustomer($id): void
    {
        $this->client->delete("customers/{$id}");
    }

    public function getActiveProjects($params = null): array
    {
        return $this->get('projects', 'project', $params);
    }

    public function getArchivedProjects($params = null): array
    {
        return $this->get('projects/archived', 'project', $params);
    }

    public function getProject($id): array
    {
        return $this->getSingle("projects/{$id}");
    }

    public function createProject($data): array
    {
        $response = $this->client->post('projects', ['project' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    public function updateProject($id, $data): array
    {
        $this->client->patch("projects/{$id}", ['project' => $data]);

        return $this->getProject($id);
    }

    public function deleteProject($id): void
    {
        $this->client->delete("projects/{$id}");
    }

    public function getActiveServices($params = null): array
    {
        return $this->get('services', 'service', $params);
    }

    public function getArchivedServices($params = null): array
    {
        return $this->get('services/archived', 'service', $params);
    }

    public function getService($id): array
    {
        return $this->getSingle("services/{$id}");
    }

    public function createService($data): array
    {
        $response = $this->client->post('services', ['service' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    public function updateService($id, $data): array
    {
        $this->client->patch("services/{$id}", ['service' => $data]);

        return $this->getService($id);
    }

    public function deleteService($id): void
    {
        $this->client->delete("services/{$id}");
    }

    public function getTimeEntries($params = null): array
    {
        return $this->get('time_entries', 'time_entry', $params);
    }

    public function getGroupedTimeEntries($groupBy, $params = null): array
    {
        if (is_array($groupBy)) {
            $groupBy = implode(',', $groupBy);
        }

        $params['group_by'] = $groupBy;

        return $this->get('time_entries', 'time_entry_group', $params);
    }

    public function getTimeEntry($id): array
    {
        return $this->getSingle("time_entries/{$id}");
    }

    public function createTimeEntry($data): array
    {
        $response = $this->client->post('time_entries', ['time_entry' => $data]);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }

    public function updateTimeEntry($id, $data): array
    {
        $this->client->patch("time_entries/{$id}", ['time_entry' => $data]);

        return $this->getTimeEntry($id);
    }

    public function deleteTimeEntry($id): void
    {
        $this->client->delete("time_entries/{$id}");
    }

    public function getActiveUsers($params = null): array
    {
        return $this->get('users', 'user', $params);
    }

    public function getArchivedUsers($params = null): array
    {
        return $this->get('users/archived', 'user', $params);
    }

    private function get(string $endpoint, string $colum, $params = null): array
    {
        $response = $this->client->get($endpoint, $params);
        $data = JSON::decode((string) $response->getBody(), true);

        return array_column($data, $colum);
    }

    private function getSingle(string $endpoint)
    {
        $response = $this->client->get($endpoint);
        $data = JSON::decode((string) $response->getBody(), true);

        return current($data);
    }
}
