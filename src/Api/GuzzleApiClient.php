<?php

declare(strict_types=1);

namespace Gamez\Mite\Api;

use Gamez\Mite\Exception\ApiClientError;
use Gamez\Mite\Support\JSON;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
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
     * @var array
     */
    private $defaultHeaders;

    /**
     * @var GuzzleClientInterface
     */
    private $client;

    private function __construct()
    {
    }

    public static function with(string $accountName, string $apiKey, array $options = null, GuzzleClientInterface $client = null): self
    {
        $options = $options ?: [];
        $userAgents = array_filter([$options['User-Agent'] ?? null, self::USER_AGENT]);

        $that = new self();

        $that->apiHost = "{$accountName}.mite.yo.lk";
        $that->defaultHeaders = [
            'Accept' => 'application/json',
            'X-MiteApiKey' => $apiKey,
            'User-Agent' => implode(' ', $userAgents),
        ];
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

    public function post($endpoint, array $data = null): ResponseInterface
    {
        return $this->request('POST', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data = null): ResponseInterface
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    public function delete(string $endpoint, array $params = null): ResponseInterface
    {
        return $this->request('DELETE', $endpoint, $params);
    }

    private function request(string $method, string $endpoint, array $params = null, array $data = null): ResponseInterface
    {
        $url = $this->createUrl($endpoint, $params);

        $headers = $this->defaultHeaders;

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

    private function createUrl(string $endpoint, array $params = null): string
    {
        $url = "{$this->apiScheme}://{$this->apiHost}/{$endpoint}";

        if (!empty($params)) {
            $url .= '?'.http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        }

        return $url;
    }

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
