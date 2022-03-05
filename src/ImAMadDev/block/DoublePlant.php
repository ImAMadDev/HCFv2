<?php

declare(strict_types=1);

namespace ImAMadDev\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\DoublePlant as DoublePlantPMMP;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class DoublePlant extends DoublePlantPMMP
{

	public function canBeReplaced() : bool{
		return false;
	}

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        return false;
    }

	public function getDrops(Item $item) : array{
		return [];
	}
}
