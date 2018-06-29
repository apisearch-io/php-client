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

namespace Apisearch\Log;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;

/**
 * File header placeholder.
 */
class Log implements HttpTransportable
{
    /**
     * @var string
     *
     * Type fatal
     */
    const TYPE_FATAL = 'fatal';

    /**
     * @var string
     *
     * Id
     */
    private $id;

    /**
     * @var string
     *
     * Type
     */
    private $type;

    /**
     * @var string
     *
     * Payload
     */
    private $payload;

    /**
     * @var int
     *
     * Occurred on
     */
    private $occurredOn;

    /**
     * Log constructor.
     *
     * @param string $id
     * @param string $type
     * @param string $payload
     * @param int    $occurredOn
     */
    public function __construct(
        string $id,
        string $type,
        string $payload,
        int $occurredOn
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
        $this->occurredOn = $occurredOn;
    }

    /**
     * Get Id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get Type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get Payload.
     *
     * @return string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * Get OccurredOn.
     *
     * @return int
     */
    public function getOccurredOn(): int
    {
        return $this->occurredOn;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'payload' => $this->payload,
            'occurred_on' => $this->occurredOn,
        ];
    }

    /**
     * Create from array.
     *
     * @param array $array
     *
     * @return self
     *
     * @throws InvalidFormatException
     */
    public static function createFromArray(array $array)
    {
        return new self(
            (string) $array['id'],
            (string) $array['type'],
            (string) $array['payload'],
            (int) $array['occurred_on']
        );
    }
}
