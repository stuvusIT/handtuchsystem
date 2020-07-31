<?php

namespace Handtuchsystem\Test\Unit\Http;

use Handtuchsystem\Application;
use Handtuchsystem\Http\HttpClientServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use GuzzleHttp\Client as GuzzleClient;

class HttpClientServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Http\HttpClientServiceProvider::register
     */
    public function testRegister()
    {
        $app = new Application();

        $serviceProvider = new HttpClientServiceProvider($app);
        $serviceProvider->register();

        /** @var GuzzleClient $guzzle */
        $guzzle = $app->make(GuzzleClient::class);
        $config = $guzzle->getConfig();

        $this->assertFalse($config['http_errors']);
        $this->assertArrayHasKey('timeout', $config);
    }
}
