<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiClientError;
use Gamez\Mite\Support\JSON;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use function GuzzleHttp\default_user_agent;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class GuzzleApiClient implements ApiClient
{
    /**
     * @var string
     */
    private $apiScheme = 'https';

    /**
     * @var string
     */
    private $apiHost;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var GuzzleClientInterface
     */
    private $client;

    private function __construct()
    {
    }

    public static function with(string $accountName, string $apiKey, GuzzleClientInterface $client = null): self
    {
        $that = new self();

        $that->apiHost = "{$accountName}.mite.yo.lk";
        $that->apiKey = $apiKey;
        $that->client = $client ?: new GuzzleClient();

        return $that;
    }

    public function head(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('HEAD', $endpoint, $params);
    }

    public function get(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('GET', $endpoint, $params);
    }

    public function post(string $endpoint, array $data = null): ResponseInterface
    {
        return $this->request('POST', $endpoint, null, $data);
    }

    public function patch(string $endpoint, array $data = null): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, null, $data);
    }

    public function delete(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    /**
     * @param array<string, numeric|string>|null $params
     * @param array<string, numeric|string>|null $data
     */
    private function request(string $method, string $endpoint, array $params = null, array $data = null): ResponseInterface
    {
        $url = $this->createUrl($endpoint, $params);

        $headers = [
            'Accept' => 'application/json',
            'X-MiteApiKey' => $this->apiKey,
            'User-Agent' => implode(' ', [self::USER_AGENT, default_user_agent()]),
        ];

        $body = '';
        if (!empty($data)) {
            $body = JSON::encode($data);
            $headers['Content-Type'] = 'application/json';
        }

        $request = $this->createRequest($method, $url, $headers, $body);

        try {
            $response = $this->client->send($request);
        } catch (ConnectException $e) {
            throw ApiClientError::fromRequestAndReason($e->getRequest(), "Unable to connect to API: {$e->getMessage()}", $e);
        } catch (RequestException $e) {
            if ($response = $e->getResponse()) {
                throw ApiClientError::fromRequestAndResponse($e->getRequest(), $response);
            }
            throw ApiClientError::fromRequestAndReason($e->getRequest(), 'The API returned an error');
        } catch (GuzzleException $e) {
            throw ApiClientError::fromRequestAndReason($request, 'The API returned an error');
        }

        if ($response->getStatusCode() >= 400) {
            throw ApiClientError::fromRequestAndResponse($request, $response);
        }

        return $response;
    }

    /**
     * @param array<string, numeric|string>|null $params
     */
    private function createUrl(string $endpoint, array $params = null): string
    {
        $url = "{$this->apiScheme}://{$this->apiHost}/{$endpoint}";

        if (!empty($params)) {
            $url .= '?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        return $url;
    }

    /**
     * @param array<string, string|string[]> $headers
     */
    private function createRequest(string $method, string $url, array $headers, string $body = ''): RequestInterface
    {
        $body = $body ?: '';

        $request = new Request($method, $url);
        $request->getBody()->write($body);

        foreach ($headers as $name => $value) {
            $request = $request->withAddedHeader($name, $value);
        }

        return $request;
    }
}
