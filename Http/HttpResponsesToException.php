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
 */

declare(strict_types=1);

namespace Apisearch\Http;

use Apisearch\Exception\ConnectionException;
use Apisearch\Exception\ForbiddenException;
use Apisearch\Exception\InvalidFormatException;
use Apisearch\Exception\InvalidTokenException;
use Apisearch\Exception\ResourceExistsException;
use Apisearch\Exception\ResourceNotAvailableException;

/**
 * Class HttpResponsesToException.
 */
trait HttpResponsesToException
{
    /**
     * Transform transportable http errors to exceptions.
     *
     * @param array  $response
     * @param string $url
     *
     * @throw TransportableException
     */
    protected static function throwTransportableExceptionIfNeeded(
        array $response,
        string $url = ''
    ) {
        if (!isset($response['code'])) {
            return;
        }

        if (is_null($response['body'])) {
            throw ConnectionException::buildConnectExceptionByUrl($url);
        }

        switch ($response['code']) {
            case ResourceNotAvailableException::getTransportableHTTPError():
                throw new ResourceNotAvailableException($response['body']['message']);
            case InvalidTokenException::getTransportableHTTPError():
                throw new InvalidTokenException($response['body']['message']);
            case InvalidFormatException::getTransportableHTTPError():
                throw new InvalidFormatException($response['body']['message']);
            case ResourceExistsException::getTransportableHTTPError():
                throw new ResourceExistsException($response['body']['message']);
            case ForbiddenException::getTransportableHTTPError():
                throw new ForbiddenException($response['body']['message']);
            case ConnectionException::getTransportableHTTPError():
                throw new ConnectionException('Apisearch returned an internal error code [500]');
        }
    }
}
