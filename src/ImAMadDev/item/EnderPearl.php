<?php

namespace ImAMadDev\item;

use ImAMadDev\player\HCFPlayer;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use ImAMadDev\entity\projectile\EnderPearl as EnderPearlEntity;
use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnderPearl extends ProjectileItem {
	
	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemIds::ENDER_PEARL, 0), "Ender Pearl");
	}
	
	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
        if($player instanceof HCFPlayer) {
            if ($player->getCooldown()->has('enderpearl')) {
                $player->getCooldown()->add('enderpearl', 16);
                return parent::onClickAir($player, $directionVector);
            } else {
                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::LIGHT_PURPLE . "enderpearl " . TextFormat::RED . "because you have a cooldown of " . $player->getCooldown()->get('enderpearl'));
                return ItemUseResult::FAIL();
            }
        } else {
            return parent::onClickAir($player, $directionVector);
        }
	}
	
	public function getMaxStackSize() : int {
		return 16;
	}
	
	public function getThrowForce() : float {
		return 1.9;
	}

    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new EnderPearlEntity($location, $thrower);
    }
}