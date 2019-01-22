# mite SDK for PHP

Interact with [mite](https://mite.yo.lk) from your PHP application.

This library comes with out of the box support for [Guzzle](http://docs.guzzlephp.org/en/stable/)
and for [HTTP clients implementing PSR-18](https://packagist.org/providers/psr/http-client-implementation).

* [Requirements](#requirements)
* [Installation](#installation)
* [Setup](#setup)
  * [Creating a mite API client based on Guzzle](#creating-a-mite-api-client-based-on-guzzle)
  * [Creating a mite API client based on a PSR-18 HTTP Client](#creating-a-mite-api-client-based-on-a-psr-18-http-client)
  * [Creating your own mite API client](#creating-your-own-mite-api-client)
  * [Caching HTTP requests to the mite API](#caching-http-requests-to-the-mite-api)
* [Usage](#usage)
  * [Simple API access](#simple-api-access)
  * [Catching errors](#catching-errors)
* [Roadmap](#roadmap)

## Requirements

- An account name (The first part of your mite account's domain, e.g. in https://**xxx**.mite.yo.lk)
- An API key (You can find your API key on https://xxx.mite.yo.lk/myself)

Please note that the capabilities of the library are limited by the permissions of the used credentials.
As an example, a user with the role "Time Tracker" can only access data that has been made available 
to them and is not allowed to create new customers and might.
 
If you want to see and do anything, use an account with administrative permissions.

---

## Installation

```bash
composer require gamez/mite
```

---

## Setup

### Creating a mite API client based on Guzzle

```bash
composer require guzzlehttp/guzzle
``` 

```php
<?php
// a file in the same directory in which you perfomed the composer command(s)
require 'vendor/autoload.php';

use Gamez\Mite\Api\GuzzleApiClient;

$accountName = 'xxx';
$apiKey = 'xxx';

$apiClient = new GuzzleApiClient($accountName, $apiKey);
```

### Creating a mite API client based on a PSR-18 HTTP Client

To be able to use a PSR-18 HTTP client, you need a 
[PSR-17 HTTP Factory](https://packagist.org/providers/psr/http-factory-implementation) as well.

If your application already has a PSR-18 HTTP client and a PSR-17 HTTP factory, skip the following
`composer require` step. Otherwise, I recommend using 

* [`nyholm/psr7`](https://github.com/Nyholm/psr7) as your PSR-17 HTTP factory
* [A HTTPlug client/adapter](http://docs.php-http.org/en/latest/clients.html) as your PSR-18 HTTP client. 

> **Note**: HTTPlug is currently in the process of making all clients and adapters ready for PSR-18. At the
> time of this writing, the only released client/adapter implementing PSR-18 is the guzzle6-adapter.
> For the following example I am using a feature branch of the cURL client so that it doesn't seem
> as if you could have taken the Guzzle API Client anyways.

```bash
composer require "php-http/curl-client:dev-issue-41-psr-18 as 2.0" nyholm/psr7
```

```php
<?php
// a file in the same directory in which you perfomed the composer command(s)
require 'vendor/autoload.php';

use Gamez\Mite\Api\HttpApiClient;
use Http\Client\Curl\Client as CurlClient;
use Nyholm\Psr7\Factory\Psr17Factory;

$accountName = 'xxx';
$apiKey = 'xxx';

$psr17Factory = new Psr17Factory();
$curlClient = new CurlClient($psr17Factory, $psr17Factory);

$apiClient = new HttpApiClient($accountName, $apiKey, $curlClient, $psr17Factory);
```

### Creating your own mite API client

If you want to create your own API client, implement the `\Gamez\Mite\Api\ApiClient` interface
and use your implementation.

### Caching HTTP requests to the mite API

To cache requests to the mite API, you can add a caching middleware/plugin to the HTTP client
before injecting it into the API client instance. See the documentation of the respective
component for instructions on how to do that.

* Guzzle: [kevinrob/guzzle-cache-middleware](https://github.com/Kevinrob/guzzle-cache-middleware)
* HTTPlug: [Cache Plugin](http://docs.php-http.org/en/latest/plugins/cache.html)

---

## Usage

### Simple API access

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
<?php

use Gamez\Mite\SimpleApi;

/** @var \Gamez\Mite\Api\ApiClient $apiClient */
$api = new SimpleApi($apiClient);

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

### Catching errors

All exceptions thrown by this library implement the `\Gamez\Mite\Exception\MiteException` interface.
Exceptions thrown while using an API Client will throw a `\Gamez\Mite\Exception\ApiClientError`.

```php
<?php 

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

---

## Roadmap

* Tests
* Interfaces and value objects
* CLI tool
* Better documentation
* Using mite XML Backup files as a data backend 

---

