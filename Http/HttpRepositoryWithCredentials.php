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

use Apisearch\Repository\RepositoryWithCredentials;

/**
 * Class HttpRepositoryWithCredentials.
 */
abstract class HttpRepositoryWithCredentials extends RepositoryWithCredentials
{
    use HttpResponsesToException;

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
}
