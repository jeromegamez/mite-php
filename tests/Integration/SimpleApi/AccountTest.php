<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Integration\SimpleApi;

use Gamez\Mite\Tests\Integration\SimpleApiTestCase;

/**
 * @internal
 *
 * @covers \Gamez\Mite\Api\HttpApiClient
 * @covers \Gamez\Mite\SimpleApi
 */
final class AccountTest extends SimpleApiTestCase
{
    /**
     * @test
     */
    public function it_retrieves_the_account(): void
    {
        $account = self::$api->getAccount();

        $this->assertArrayStructure(
            ['id', 'name', 'title', 'currency', 'created_at', 'updated_at'],
            $account,
        );

        self::assertSame(self::$accountName, $account['name']);
    }

    /**
     * @test
     */
    public function it_retrieves_the_current_user(): void
    {
        $user = self::$api->getMyself();

        $this->assertUserStructure($user);
    }
}
