<?php

namespace ImAMadDev\item;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use ImAMadDev\entity\projectile\EnderPearl as EnderPearlEntity;
use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class EnderPearl extends ProjectileItem {
	
	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemIds::ENDER_PEARL, 0), "Ender Pearl");
	}
	
	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
		return ItemUseResult::SUCCESS();
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