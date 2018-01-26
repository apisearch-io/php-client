<?php

/*
 * This file is part of the Apisearch PHP Client.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Http;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;
use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class HttpRepositoryWithCredentials.
 */
abstract class HttpRepositoryWithCredentials extends RepositoryWithCredentials
{
    /**
     * @var HttpClient
     *
     * Http client
     */
    protected $httpClient;

    /**
     * HttpAdapter constructor.
     *
     * @param HttpClient $httpClient
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Transform transportable http errors to exceptions.
     *
     * @param array $response
     *
     * @throw TransportableException
     */
    protected function throwTransportableExceptionIfNeeded(array $response)
    {
        switch ($response['code']) {
            case ResourceNotAvailableException::getTransportableHTTPError():
                throw new ResourceNotAvailableException($response['body']['message']);
            case InvalidFormatException::getTransportableHTTPError():
                throw new InvalidFormatException($response['body']['message']);
            case ResourceExistsException::getTransportableHTTPError():
                throw new ResourceExistsException($response['body']['message']);
        }
    }
}
