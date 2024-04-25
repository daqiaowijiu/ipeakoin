<?php

namespace Kzz\Ipeakoin;

use Kzz\Ipeakoin\Exceptions\InvalidConfigException;

class CoreSource extends Client
{

    /**
     * @param \Kzz\Ipeakoin\Config|array $config
     *
     * @throws \Kzz\Ipeakoin\Exceptions\InvalidConfigException
     */
    public function __construct($config)
    {
        if (!($config instanceof Config)) {
            $config = new Config($config);
        }


        parent::__construct($config);

        $this->setBaseUri(\sprintf(
            'https://%s/%s/%s/',
            $config->get('base_uri', self::DEFAULT_URI),
            $config->get('uri_prefix', self::DEFAULT_URI_PREFIX),
            $config->get('api_version', self::DEFAULT_API_VERSION),
        ));
    }

    public function listAccountFeeRates()
    {
        return $this->get('accounts/fees');
    }

    public function createAccount($body)
    {
        return $this->post('accounts/register', ['body' => json_encode($body)]);
    }


}

