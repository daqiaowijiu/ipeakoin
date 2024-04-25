<?php

namespace Kzz\Ipeakoin\Middleware;

use Kzz\Ipeakoin\Auth;
use Kzz\Ipeakoin\AuthClient;
use Kzz\Ipeakoin\Config;
use Psr\Http\Message\RequestInterface;

class CreateRequestAuthorization
{
    protected Config $config;



    /**
     * WithSignature constructor.
     *
     * @param  string  $secretId
     * @param  string  $secretKey
     * @param  string|null  $signatureExpires
     */
    public function __construct($config)
    {
        $this->config = $config;

    }

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $request = $request->withHeader(
                'x-ipeakoin-auth-token',
                (new Auth($this->config))
                    ->createAuthorizationHeader($request)
            );

            return $handler($request, $options);
        };
    }
}
