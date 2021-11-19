<?php

namespace ImAMadDev\utils;

use pocketmine\player\Player;
use pocketmine\entity\Location;

class NBT {

    /**
     * @param Player $player
     * @return Location
     */
	public static function createWith(Player $player) : Location {
        $y = $player->getPosition()->y + $player->getEyeHeight();
        return new Location($player->getPosition()->x, $y, $player->getPosition()->z, $player->getPosition()->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
	}
}