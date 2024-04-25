<?php

namespace Kzz\Ipeakoin\Exceptions;

use Kzz\Ipeakoin\Http\Response;
use Psr\Http\Message\ResponseInterface;

class ServerException extends Exception
{
    protected \GuzzleHttp\Exception\ServerException $guzzleServerException;

    public function __construct(\GuzzleHttp\Exception\ServerException $guzzleServerException)
    {
        $this->guzzleServerException = $guzzleServerException;

        parent::__construct($guzzleServerException->getMessage(), $guzzleServerException->getCode(), $guzzleServerException->getPrevious());
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface|\Kzz\Ipeakoin\Http\Response|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return new Response($this->guzzleServerException->getResponse());
    }
}
