<?php

declare(strict_types=1);

namespace Gamez\Mite\Tests\Support;

use Gamez\Mite\Api\ApiClient;
use Gamez\Mite\Api\ApiClientFactory;
use Gamez\Mite\Api\HttpApiClient;
use Http\Client\Common\PluginClient;
use Http\Client\Plugin\Vcr\NamingStrategy\PathNamingStrategy;
use Http\Client\Plugin\Vcr\Recorder\FilesystemRecorder;
use Http\Client\Plugin\Vcr\RecordPlugin;
use Http\Client\Plugin\Vcr\ReplayPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;

final class VCRApiClientFactory implements ApiClientFactory
{
    /**
     * @param non-empty-string $filePath
     */
    public function __construct(
        private readonly string $filePath,
        private readonly bool $throwIfNotAbleToReplay,
    ) {}

    public function __invoke(string $accountName, string $apiKey): ApiClient
    {
        return HttpApiClient::with(
            $accountName,
            $apiKey,
            self::createVCRClient($this->filePath, $this->throwIfNotAbleToReplay),
            Psr17FactoryDiscovery::findRequestFactory(),
        );
    }

    /**
     * @param non-empty-string $filePath
     */
    private static function createVCRClient(string $filePath, bool $throwIfNotAbleToReplay): ClientInterface
    {
        $namingStrategy = new PathNamingStrategy([
            'hash_headers' => ['X-Beste-Caller'],
        ]);
        $recorder = new FilesystemRecorder($filePath);
        $appendHeader = new AppendCallerHeaderPlugin('X-Beste-Caller', 'Gamez\Mite\Tests\Integration');
        $record = new RecordPlugin($namingStrategy, $recorder);
        $replay = new ReplayPlugin($namingStrategy, $recorder, $throwIfNotAbleToReplay);

        return new PluginClient(
            Psr18ClientDiscovery::find(),
            [$appendHeader, $replay, $record],
        );
    }
}
