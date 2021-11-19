<?php

declare(strict_types=1);

namespace ImAMadDev\block;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Flowable;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\block\Flower as FlowerPMMP;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class Flower extends FlowerPMMP{

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        return false;
    }

	public function onNearbyBlockChange() : void{
		if($this->getSide(Facing::DOWN)->isTransparent()){
			$this->getPosition()->getWorld()->useBreakOn($this->getPosition());
		}
	}

	public function getFlameEncouragement() : int{
		return 60;
	}
	
	public function getDrops(Item $item) : array{
		return [];
	}

	public function getFlammability() : int{
		return 100;
	}
}
