<?php

namespace Kzz\Ipeakoin;

use GuzzleHttp\ClientInterface;
use Kzz\Ipeakoin\Exceptions\ClientException;
use Kzz\Ipeakoin\Exceptions\Exception;
use Kzz\Ipeakoin\Exceptions\InvalidConfigException;
use Kzz\Ipeakoin\Exceptions\ServerException;
use Kzz\Ipeakoin\Http\Response;
use Kzz\Ipeakoin\Traits\CreatesHttpClient;

/**
 * @method \Kzz\Ipeakoin\Http\Response get($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response head($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response options($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response put($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response post($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response patch($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response delete($uri, array $options = [])
 * @method \Kzz\Ipeakoin\Http\Response request(string $method, $uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface getAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface headAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface optionsAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface putAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface postAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface patchAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface deleteAsync($uri, array $options = [])
 * @method \GuzzleHttp\Promise\PromiseInterface requestAsync(string $method, $uri, array $options = [])
 */
class NoAuthClient
{
    use CreatesHttpClient;

    protected Config $config;

    protected \GuzzleHttp\Client $client;
    public const DEFAULT_URI = 'api-sandbox.ipeakoin.com';
    public const DEFAULT_URI_PREFIX = 'open-api';
    public const DEFAULT_API_VERSION = 'v1';

    /**
     * @param $config
     * @throws InvalidConfigException
     */
    public function __construct($config)
    {
        if (!($config instanceof Config)) {
            $config = new Config($config);
        }

        if (!$config->has('client_id') || !$config->has('client_secret')) {
            throw new InvalidConfigException('client_id, client_secret was required.');
        }

        $this->config = $config;

        $this->mergeHttpClientOptions($config->get('guzzle', []));
        $this->configureUserAgent($config);

    }

    public function getAppId(): int
    {
        return $this->config->get('app_id', 0);
    }

    public function getClientId(): string
    {
        return $this->config->get('client_id', '');
    }

    public function getClientSecret(): string
    {
        return $this->config->get('client_secret', '');
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getHttpClient(): \GuzzleHttp\Client
    {
        return $this->client ?? $this->client = $this->createHttpClient();

    }

    public function __call($method, $arguments)
    {

        try {
            return new Response(\call_user_func_array([$this->getHttpClient(), $method], $arguments));
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            throw new ClientException($e);
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            throw new ServerException($e);
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    public static function spy()
    {
        return \Mockery::mock(static::class);
    }

    public static function partialMock()
    {
        $mock = \Mockery::mock(static::class)->makePartial();
        $mock->shouldReceive('getHttpClient')->andReturn(\Mockery::mock(\GuzzleHttp\Client::class));

        return $mock;
    }

    public static function partialMockWithConfig(Config $config, array $methods)
    {
        $mock = \Mockery::mock(static::class . \sprintf('[%s]', \join(',', $methods)), [$config]);
        $mock->shouldReceive('getHttpClient')->andReturn(\Mockery::mock(\GuzzleHttp\Client::class));

        return $mock;
    }

    /**
     * @param \Kzz\Ipeakoin\Config $config
     *
     * @return \Kzz\Ipeakoin\Client
     */
    protected function configureUserAgent(Config $config): NoAuthClient
    {
        $this->setHeader('User-Agent', $config->get('guzzle.headers.User-Agent', 'kzz/ipeakoin:' . ClientInterface::MAJOR_VERSION));

        return $this;
    }
}
