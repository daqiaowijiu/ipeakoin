<?php

namespace Kzz\Ipeakoin;

class Auth
{


    public $config;


    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     * @throws Exceptions\InvalidConfigException
     */
    public function createAuthorizationHeader(): string
    {
        if ($this->config->has('access_token')) {
            return $this->config->get('access_token');
        }

        $authClient = new AuthClient($this->config);
        $codeRes    = $authClient->getCode();
        $tokenRes   = $authClient->accessToken($codeRes['code']);
        return $tokenRes['access_token'];

    }

}
