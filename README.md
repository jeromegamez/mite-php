# mite SDK for PHP

Interact with [mite](https://mite.yo.lk) from your PHP application.

[![Current version](https://img.shields.io/packagist/v/gamez/mite.svg)](https://packagist.org/packages/gamez/mite)
[![Supported PHP version](https://img.shields.io/packagist/php-v/gamez/mite.svg)]()
[![Build Status](https://travis-ci.com/jeromegamez/mite-php.svg?branch=main)](https://travis-ci.com/jeromegamez/mite-php)

---

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
  * [Basic API client](#basic-api-client)
  * [Simple API](#simple-api)
  * [Simple Tracker](#simple-tracker)
  * [Catching errors](#catching-errors)
  * [Caching HTTP requests](#caching-http-requests)
  * [Creating your own API client](#creating-your-own-api-client)
* [License](#license)

---

## Requirements

- An account name (The first part of your mite account's domain, e.g. in https://**xxx**.mite.yo.lk)
- An API key (You can find your API key on https://xxx.mite.yo.lk/myself)

Please note that the capabilities of the library are limited by the permissions of the used credentials.
As an example, a user with the role "Time Tracker" can only access data that has been made available 
to them and is not allowed to create new customers.
 
Users with the `admin` role can see and do (almost) everything.

---

## Installation

In order to use this library, you need a [PSR-18 HTTP Client](https://packagist.org/providers/psr/http-client-implementation), and a
[PSR-17 HTTP Message Factory](https://packagist.org/providers/psr/http-factory-implementation).

### Example using `kriswallsmith/buzz` and `nyholm/psr7`

```bash
composer require kriswallsmith/buzz nyholm/psr7 gamez/mite
```

```php
$requestFactory = new \Nyholm\Psr7\Factory\Psr17Factory();
$httpClient = new \Buzz\Client\FileGetContents($requestFactory);
```

### Example using `guzzlehttp/guzzle` and `laminas/laminas-diactoros`

```bash
composer require guzzlehttp/guzzle laminas/laminas-diactoros gamez/mite
```

```php
$requestFactory = new \Laminas\Diactoros\RequestFactory();
$httpClient = new \GuzzleHttp\Client();
```

---

## Usage

### Basic API client

Once you have created an HTTP Client and Request Factory as described in the installation section,
you can create an API client with them:

```php
use Gamez\Mite\Api\HttpApiClient;

$accountName = 'xxx';
$apiKey = 'xxx';

/**
 * @var \Psr\Http\Message\RequestFactoryInterface $requestFactory
 * @var \Psr\Http\Client\ClientInterface $httpClient
 */
$apiClient = HttpApiClient::with($accountName, $apiKey, $httpClient, $requestFactory);
```

This API client allows you to make authenticated HTTP requests to the API of your mite account - 
see [mite's REST API documentation](https://mite.yo.lk/en/api/) for the endpoints you can use.

### Simple API

[`Gamez\Mite\SimpleApi`](./src/SimpleApi.php) is the easiest and fastest way to access the data in your 
mite account. Its methods are named after the [available REST API endpoints](https://mite.yo.lk/en/api/) 
and always return arrays of data. You can inspect the available methods by looking at the
[source code of the `Gamez\Mite\SimpleApi` class](./src/SimpleApi.php) or by using the 
autocompletion features of your IDE.

The Simple API doesn't get in your way when accessing the mite API, but it doesn't provide additional 
features either. It will, for example, not tell you if you used a wrong query parameter or invalid
field value, so you will have to rely on the returned API responses.

For information on which query parameters and field values are allowed, see 
[official mite API documentation](https://mite.yo.lk/en/api/)

#### Example

```php
use Gamez\Mite\SimpleApi;

/** 
 * @var \Gamez\Mite\Api\ApiClient $apiClient 
 */
$api = SimpleApi::withApiClient($apiClient);

$customer = $api->createCustomer([
    'name' => 'My new customer',
    'note' => 'He pays better than the old one',
]);

echo 'Customer: '.print_r($customer, true);

$project = $api->createProject([
    'name' => 'My new customer project',
    'customer_id' => $customer['id'],
    'budget_type' => 'minutes_per_month',
    'budget' => 6000,
    'hourly_rate' => 10000,
    'active_hourly_rate' => 'hourly_rate',
]);

echo 'Project: '.print_r($project, true);

$service = $api->createService([
    'name' => 'Customer Support',
]);

echo 'Service: '.print_r($service, true);

$user = current($api->getActiveUsers()); // For the sake of this example, we use the first available user

echo 'User: '.print_r($user, true);

$timeEntry = $api->createTimeEntry([
    'date_at' => 'today',
    'minutes' => 60,
    'user_id' => $user['id'], // Would we omit this, the authenticated user would be used
    'project_id' => $project['id'],
    'service_id' => $service['id'],
    'note' => 'We had some work to do, and we did some work.'
]);

echo 'Time Entry: '.print_r($timeEntry, true);

// $api->delete($newTimeEntry['id'];

$workdaysPerMonthAndUser = $api->getGroupedTimeEntries($groupBy = ['user'], ['at' => 'this_month']);

echo 'Workdays per month and user: '.print_r($workdaysPerMonthAndUser, true);
```

### Simple Tracker

[`Gamez\Mite\SimpleTracker`](./src/SimpleTracker.php) allows you to work with mite's time tracker.

**Note:** You can only access the tracker of the currently authenticated user (identified by the used API key).
It is not possible to modify trackers of other users. 

Each action on the tracker returns an array with information about the tracked time entry, but you don't have
to inspect the result to know if the action has been successful or not - if an action does not throw an 
error, it has been successful.

```php
use Gamez\Mite\SimpleApi;
use Gamez\Mite\SimpleTracker;

/** @var \Gamez\Mite\Api\ApiClient $apiClient */
$api = SimpleApi::withApiClient($apiClient);
$tracker = SimpleTracker::withApiClient($apiClient);

$sleeping = $api->createTimeEntry(['note' => 'I am sleeping']);
$working = $api->createTimeEntry(['note' => 'I switch to this now and then']);

$tracker->start($sleeping['id']);
sleep(1); // You don't need this sleep, but the example makes more sense this way
$tracker->start($working['id']); // This will automatically stop the "sleeping" tracker
// No sleep this time, we'll just work for zero seconds 
$tracker->stop($working['id']); // We stopped working!

print_r($tracker->status()); // Sad
```

### Catching errors

All exceptions thrown by this library implement the `\Gamez\Mite\Exception\MiteException` interface.
Exceptions thrown while using an API Client will throw a `\Gamez\Mite\Exception\ApiClientError`.

```php
use Gamez\Mite\Exception\ApiClientError;
use Gamez\Mite\Exception\MiteException;

try {
    /** @var \Gamez\Mite\Api\ApiClient $apiClient */
    $result = $apiClient->get('nice-try');
} catch (ApiClientError $e) {
    $message = "Something went wrong while accessing {$e->getRequest()->getUri()}";

    if ($response = $e->getResponse()) {
        $message .= " ({$response->getStatusCode()})";
    }

    $message .= ' : '.$e->getMessage();

    exit($message);
} catch (MiteException $e) {
    exit('Something not API related went really wrong: '.$e->getMessage());
}

// Something went wrong while accessing https://xxx.mite.yo.lk/nice-try (404) :
// The URI /nice-try could not be found.
```

### Caching HTTP requests

To cache HTTP requests to the API, you can add a caching middleware/plugin to the HTTP client
before injecting it into the API client instance. See the documentation of the respective
component for instructions on how to do that.

* Guzzle: [kevinrob/guzzle-cache-middleware](https://github.com/Kevinrob/guzzle-cache-middleware)
* HTTPlug: [Cache Plugin](http://docs.php-http.org/en/latest/plugins/cache.html)

### Creating your own API client

If you want to create your own API client, implement the `\Gamez\Mite\Api\ApiClient` interface
and use your implementation.

## License

`gamez/mite` is licensed under the [MIT License](LICENSE).

Your use of mite is governed by the [Terms of Service for mite.](https://mite.yo.lk/en/terms.html).
