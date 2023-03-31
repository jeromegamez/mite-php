<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Integration;

use Gamez\Mite\SimpleApi;

/**
 * @internal
 */
abstract class SimpleApiTestCase extends IntegrationTestCase
{
    protected static SimpleApi $api;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$api = SimpleApi::withApiClient(self::$apiClient);
    }
}
