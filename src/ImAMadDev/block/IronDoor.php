<?php

declare(strict_types=1);

namespace ImAMadDev\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\block\Door;
use pocketmine\block\BlockToolType;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class IronDoor extends Door{

	public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::IRON_DOOR_BLOCK, 0, ItemIds::IRON_DOOR), "Iron Door", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 25.0));
	}

	public function getName() : string{
		return "Iron Door";
	}
	
	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        return false;
    }
}
