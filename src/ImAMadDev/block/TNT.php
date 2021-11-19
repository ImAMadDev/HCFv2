<?php

declare(strict_types=1);

namespace ImAMadDev\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\TNT as TNTPM;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class TNT extends TNTPM{

	public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::TNT, 0), "TNT", BlockBreakInfo::instant());
	}

	public function getName() : string{
		return "TNT";
	}

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        return false;
    }

	public function hasEntityCollision() : bool{
		return true;
	}

    public function onEntityInside(Entity $entity) : bool{
        return true;
    }

    /**
     * @param int $fuse
     * @return void
     */
	public function ignite(int $fuse = 80) : void {
	}

	public function getFlameEncouragement() : int{
		return 15;
	}

	public function getFlammability() : int{
		return 100;
	}

	public function onIncinerate() : void{
	}
}
