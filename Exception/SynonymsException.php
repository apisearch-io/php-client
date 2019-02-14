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

namespace Apisearch\Exception;

use Exception;

/**
 * Class SynonymsException.
 */
class SynonymsException extends Exception
{
    /**
     * Synonyms file not found.
     *
     * @param string $synonymsFile
     *
     * @return SynonymsException
     */
    public static function synonymsFileNotFound(string $synonymsFile): self
    {
        return new self(sprintf('File %s not found', $synonymsFile));
    }

    /**
     * Synonyms malformed data.
     *
     * @param string $synonymsFile
     *
     * @return SynonymsException
     */
    public static function synonymsMalformedData(string $synonymsFile): self
    {
        return new self(sprintf('File %s has malformed data', $synonymsFile));
    }
}
