<?php

namespace Kzz\Ipeakoin\Exceptions;

use Kzz\Ipeakoin\Http\Response;
use Psr\Http\Message\ResponseInterface;

class ClientException extends Exception
{
    protected \GuzzleHttp\Exception\ClientException $guzzleClientException;

    public function __construct(\GuzzleHttp\Exception\ClientException $guzzleServerException)
    {
        $this->guzzleClientException = $guzzleServerException;

        parent::__construct($guzzleServerException->getMessage(), $guzzleServerException->getCode(), $guzzleServerException->getPrevious());
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|\Kzz\Ipeakoin\Http\Response|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return new Response($this->guzzleClientException->getResponse());
    }
}
