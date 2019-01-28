# Changelog

## Unreleased

* The signatures of the following methods have changed
  * `GuzzleApiClient::with(string $accountName, string $apiKey, GuzzleClientInterface $client = null)`
    + Removed the `$options` parameter. If you want to modify the behaviour of the underlying GuzzleHTTP client,
      configure it directly.  
  * `HttpApiClient::with(string $accountName, string $apiKey, ClientInterface $client, RequestFactoryInterface $requestFactory)`
    + Removed the `$options` parameter. If you want to modify the behaviour of the underlying HTTP client,
      configure it directly.

## 1.0.1 - 2019-01-23

* Removed superfluous bracket in the API client's user agent string 

## 1.0 - 2019-01-23

Initial release
