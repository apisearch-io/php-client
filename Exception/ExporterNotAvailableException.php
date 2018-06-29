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

use LogicException;

/**
 * Class ExporterNotAvailableException.
 */
class ExporterNotAvailableException extends LogicException
{
    /**
     * Create exporter not available because of missing dependency.
     *
     * @param string $format
     * @param string $missingClass
     * @param string $package
     *
     * @return ExporterNotAvailableException
     */
    public static function createForMissingDependency(
        string $format,
        string $missingClass,
        string $package
    ): self {
        return new self(sprintf('Exporter %s not available. Missing class %s. To resolve it, add %d in your composer',
            $format,
            $missingClass,
            $package
        ));
    }
}
