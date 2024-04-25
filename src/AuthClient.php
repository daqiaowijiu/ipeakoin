<?php

namespace Kzz\Ipeakoin;


use Kzz\Ipeakoin\Exceptions\InvalidConfigException;


class AuthClient extends NoAuthClient
{

    /**
     * @param $config
     * @throws InvalidConfigException
     */
    public function __construct($config)
    {
        if (!($config instanceof Config)) {
            $config = new Config($config);
        }


        parent::__construct($config);

        $this->setBaseUri(\sprintf(
            'https://%s/%s/',
            $config->get('base_uri', self::DEFAULT_URI),
            $config->get('uri_prefix', self::DEFAULT_URI_PREFIX),

        ));
    }


    public function getCode()
    {
        $query = ['query' => [
            'clientId' => $this->config->get('client_id'),
        ]];
        return $this->get('oauth/authorize', $query);
    }

    public function accessToken(string $code)
    {
        $body = [
            'clientId'     => $this->config->get('client_id'),
            'clientSecret' => $this->config->get('client_secret'),
            'code'         => $code
        ];
        return $this->post('oauth/access-token', ['body' => json_encode($body)]);
    }

    public function refreshToken(string $token)
    {
        $body = [
            'clientId'     => $this->config->get('client_id'),
            'refreshToken' => $token,
        ];
        return $this->post('oauth/refresh-token', ['body' => json_encode($body)]);
    }



}

