<?php


namespace ImAMadDev\ticks;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Block;
use pocketmine\scheduler\Task;
use ImAMadDev\HCF;
use pocketmine\world\World;

class ReplaceBlockTick extends Task {

	public function __construct(
        public HCF $main,
        public Block $block,
        public World $world,
        public int $replaceId = BlockLegacyIds::AIR
    ){}

	public function onRun() : void {
		if($this->world->getBlockAt($this->block->getPosition()->x, $this->block->getPosition()->y, $this->block->getPosition()->z)->getId() === $this->block->getId()) {
			$this->world->setBlockAt($this->block->getPosition()->x, $this->block->getPosition()->y, $this->block->getPosition()->z, BlockFactory::getInstance()->get($this->replaceId));
		}
	}
}