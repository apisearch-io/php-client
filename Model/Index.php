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

namespace Apisearch\Model;

use Apisearch\Exception\InvalidFormatException;

/**
 * Class Coordinate.
 */
class Index implements HttpTransportable
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $docCount;

    /**
     * GeoPoint constructor.
     *
     * @param string $appId
     * @param string $name
     * @param int    $docCount
     */
    public function __construct(
        string $appId,
        string $name,
        int $docCount = 0
    ) {
        $this->appId = $appId;
        $this->name = $name;
        $this->docCount = $docCount;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDocCount(): int
    {
        return $this->docCount;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'app_id' => $this->appId,
            'name' => $this->name,
            'doc_count' => $this->docCount,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return Index
     */
    public static function createFromArray(array $array): self
    {
        if (!isset($array['app_id'], $array['name'])) {
            throw InvalidFormatException::indexFormatNotValid();
        }

        return new self(
            $array['app_id'],
            $array['name'],
            isset($array['doc_count']) ? (int) $array['doc_count'] : 0
        );
    }
}
