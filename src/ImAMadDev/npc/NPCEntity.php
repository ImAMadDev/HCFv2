<?php

namespace ImAMadDev\npc;

use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

abstract class NPCEntity extends Human {

    protected bool $canUpdateTag = true;

    public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $skin, $nbt);
    }

	public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void
    {
    }
	
	public function entityBaseTick(int $tickDiff = 1): bool {
	    if($this->canUpdateTag) {
            $this->setNameTag($this->getName());
            $this->setNameTagVisible(true);
            $this->setNameTagAlwaysVisible(true);
        }
		return parent::entityBaseTick($tickDiff);
	}

	public function attack(EntityDamageEvent $source): void {
		$source->cancel();
	}

}