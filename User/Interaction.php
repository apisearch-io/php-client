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
     * @var int
     *
     * Weight
     */
    private $weight;

    /**
     * Interaction constructor.
     *
     * @param User     $user
     * @param ItemUUID $itemUUID
     * @param          $weight
     */
    public function __construct(
        User $user,
        ItemUUID $itemUUID,
        int $weight
    ) {
        $this->user = $user;
        $this->itemUUID = $itemUUID;
        $this->weight = $weight;
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
     * Get Weight.
     *
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * To array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user->toArray(),
            'item_uuid' => $this->itemUUID->toArray(),
            'weight' => $this->weight,
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
            User::createFromArray($array['user']),
            ItemUUID::createFromArray($array['item_uuid']),
            $array['weight']
        );
    }
}
