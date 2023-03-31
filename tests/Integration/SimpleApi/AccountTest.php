<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Integration\SimpleApi;

use Gamez\Mite\Tests\Integration\SimpleApiTestCase;

/**
 * @covers \Gamez\Mite\SimpleApi
 * @covers \Gamez\Mite\Api\HttpApiClient
 *
 * @internal
 */
final class AccountTest extends SimpleApiTestCase
{
    public function testItRetrievesTheAccount(): void
    {
        $account = self::$api->getAccount();

        $this->assertArrayStructure(
            ['id', 'name', 'title', 'currency', 'created_at', 'updated_at'],
            $account,
        );

        $this->assertSame(self::$accountName, $account['name']);
    }

    public function testItRetrievesTheCurrentUser(): void
    {
        $user = self::$api->getMyself();

        $this->assertUserStructure($user);
    }
}
