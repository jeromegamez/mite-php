# Changelog

## 2.1.0 - 2023-02-20

* Changed API domain from `mite.yo.lk` to `mite.de`
  (see [mite.de blog post](https://mite.de/en/blog/2023/02/14/upcoming-move-to-mite-de/))
  ([#5](https://github.com/jeromegamez/mite-php/pull/5))

## 2.0.1 - 2021-04-16

* Fixed typo in `HttpApiClient` causing invalid host names 
  ([#2](https://github.com/jeromegamez/mite-php/pull/2))

## 2.0 - 2021-04-15

* Dropped support for PHP <7.4
* Removed the `GuzzleApiClient` implementation, because Guzzle implements PSR-18 since release 7.0. If you
  used the `GuzzleApiClient`, please refer to the updated installation instructions in the README.
  
## 1.1.1 - 2021-04-15 

* Fixed data being used as URL params for POST and PATCH requests

## 1.1 - 2021-04-15

* Added support for PHP 8.0
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
