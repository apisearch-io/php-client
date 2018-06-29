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

namespace Apisearch\Repository;

use Apisearch\Token\Token;

/**
 * Class WithTokenTrait.
 */
trait WithTokenTrait
{
    /**
     * @var Token
     *
     * Token
     */
    private $token;

    /**
     * Get Token.
     *
     * @return null|Token
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * Set Token.
     *
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }
}
