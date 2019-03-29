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

namespace Apisearch\User;

use Apisearch\Exception\InvalidFormatException;
use Apisearch\Model\HttpTransportable;
use Apisearch\Model\ItemUUID;
use Apisearch\Model\User;

/**
 * Class Interaction.
 */
class Interaction implements HttpTransportable
{
    /**
     * @var User
     *
     * User
     */
    private $user;

    /**
     * @var ItemUUID
     *
     * Item uuid
     */
    private $itemUUID;

    /**
     * @var string
     *
     * Event name
     */
    private $eventName;

    /**
     * @var array
     *
     * Metadata
     */
    private $metadata;

    /**
     * Interaction constructor.
     *
     * @param User     $user
     * @param ItemUUID $itemUUID
     * @param string   $eventName
     * @param array    $metadata
     */
    public function __construct(
        User $user,
        ItemUUID $itemUUID,
        string $eventName,
        array $metadata = []
    ) {
        $this->user = $user;
        $this->itemUUID = $itemUUID;
        $this->eventName = $eventName;
        $this->metadata = $metadata;
    }

    /**
     * Get User.
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get ItemUUID.
     *
     * @return ItemUUID
     */
    public function getItemUUID(): ItemUUID
    {
        return $this->itemUUID;
    }

    /**
     * Get event name.
     *
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * Get Metadata.
     *
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'user' => $this->user->toArray(),
            'item_uuid' => $this->itemUUID->toArray(),
            'event_name' => $this->eventName,
            'metadata' => $this->metadata,
        ]);
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
            User::createFromArray($array['user']),
            ItemUUID::createFromArray($array['item_uuid']),
            (string) $array['event_name'],
            $array['metadata'] ?? []
        );
    }
}
