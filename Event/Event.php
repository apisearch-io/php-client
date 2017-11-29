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

namespace Apisearch\Event;

use Apisearch\Model\HttpTransportable;

/**
 * Class Event.
 */
class Event implements HttpTransportable
{
    /**
     * var string.
     *
     * Consistency hash
     */
    private $consistencyHash;

    /**
     * @var string
     *
     * name
     */
    private $name;

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
     * Event constructor.
     *
     * @param string $consistencyHash
     * @param string $name
     * @param string $payload
     * @param int    $occurredOn
     */
    private function __construct(
        string $consistencyHash,
        string $name,
        string $payload,
        int $occurredOn
    ) {
        $this->consistencyHash = $consistencyHash;
        $this->name = $name;
        $this->payload = $payload;
        $this->occurredOn = $occurredOn;
    }

    /**
     * Get ConsistencyHash.
     *
     * @return string
     */
    public function getConsistencyHash(): string
    {
        return $this->consistencyHash;
    }

    /**
     * Get Name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
            'consistency_hash' => $this->consistencyHash,
            'name' => $this->name,
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
     */
    public static function createFromArray(array $array)
    {
        return self::createByPlainData(
            (string) $array['consistency_hash'],
            (string) $array['name'],
            (string) $array['payload'],
            (int) $array['occurred_on']
        );
    }

    /**
     * Event constructor.
     *
     * @param null|Event $previousEvent
     * @param string     $name
     * @param string     $payload
     * @param int        $occurredOn
     *
     * @return Event
     */
    public static function createByPreviousEvent(
        ? Event $previousEvent,
        string $name,
        string $payload,
        int $occurredOn
    ): Event {
        $lastEventUUID = $previousEvent instanceof self
            ? $previousEvent->getConsistencyHash()
            : '';

        return new self(
            hash('sha256', $lastEventUUID.$name.$payload.$occurredOn),
            $name,
            $payload,
            $occurredOn
        );
    }

    /**
     * Event constructor.
     *
     * @param string $consistencyHash
     * @param string $name
     * @param string $payload
     * @param int    $occurredOn
     *
     * @return Event
     */
    public static function createByPlainData(
        string $consistencyHash,
        string $name,
        string $payload,
        int $occurredOn
    ): Event {
        $event = new self(
            $consistencyHash,
            $name,
            $payload,
            $occurredOn
        );

        return $event;
    }
}
