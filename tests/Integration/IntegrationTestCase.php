<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Integration;

use Gamez\Mite\Api\ApiClient;
use Gamez\Mite\Api\HttpApiClient;
use Gamez\Mite\Tests\Support\AppendCallerHeaderPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Plugin\Vcr\NamingStrategy\PathNamingStrategy;
use Http\Client\Plugin\Vcr\Recorder\FilesystemRecorder;
use Http\Client\Plugin\Vcr\RecordPlugin;
use Http\Client\Plugin\Vcr\ReplayPlugin;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

/**
 * @internal
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static ApiClient $apiClient;
    protected static string $accountName;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $accountName = trim($_ENV['MITE_ACCOUNT'] ?? 'beste');
        $apiKey = trim($_ENV['MITE_API_KEY'] ?? '');

        $throwIfNotAbleToReplay = false;

        if ($accountName === '' || $apiKey === '') {
            $throwIfNotAbleToReplay = true;
        }

        self::$accountName = $accountName;

        self::$apiClient = self::createVCRApiClient(
            $accountName,
            $apiKey,
            __DIR__.'/../fixtures/vcr',
            $throwIfNotAbleToReplay
        );
    }

    protected static function createVCRApiClient(string $accountName, string $apiKey, string $filePath, bool $throwIfNotAbleToReplay): ApiClient
    {
        return HttpApiClient::with(
            $accountName,
            $apiKey,
            self::createVCRClient($filePath, $throwIfNotAbleToReplay),
            Psr17FactoryDiscovery::findRequestFactory(),
        );
    }

    protected static function createVCRClient(string $filePath, bool $throwIfNotAbleToReplay): ClientInterface
    {
        $namingStrategy = new PathNamingStrategy([
            'hash_headers' => ['X-Beste-Caller'],
        ]);
        $recorder = new FilesystemRecorder($filePath);
        $appendHeader = new AppendCallerHeaderPlugin('X-Beste-Caller', 'Gamez\Mite\Tests\Integration');
        $record = new RecordPlugin($namingStrategy, $recorder);
        $replay = new ReplayPlugin($namingStrategy, $recorder, $throwIfNotAbleToReplay);

        return new PluginClient(
            HttpClientDiscovery::find(),
            [$appendHeader, $replay, $record],
        );
    }

    protected function assertArrayStructure(array $expected, array $actual): void
    {
        Assert::assertEqualsCanonicalizing($expected, array_keys($actual));
    }

    protected function assertUserStructure(array $actual): void
    {
        $this->assertArrayStructure(
            [
                'id',
                'name',
                'email',
                'note',
                'archived',
                'role',
                'language',
                'created_at',
                'updated_at',
            ],
            $actual,
        );
    }

    protected function assertCustomerStructure(array $actual): void
    {
        $this->assertArrayStructure(
            [
                'id',
                'name',
                'note',
                'active_hourly_rate',
                'hourly_rate',
                'archived',
                'hourly_rates_per_service',
                'created_at',
                'updated_at',
            ],
            $actual,
        );

        $this->assertIsList($actual['hourly_rates_per_service']);
    }
}
