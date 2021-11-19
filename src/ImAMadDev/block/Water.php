<?php

namespace ImAMadDev\block;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\block\BlockToolType;
use pocketmine\block\BlockFactory;
use pocketmine\block\Transparent;
use pocketmine\block\Liquid;
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class Water extends Liquid {
	
	protected $id = self::FLOWING_WATER;
	
	public function __construct(int $meta = 0) {
		$this->meta = $meta;
	}
	
	public function getStillForm(): Block {
        return BlockFactory::get(Block::STILL_WATER, $this->meta);
    }

    public function getFlowingForm(): Block
    {
        return BlockFactory::get(Block::FLOWING_WATER, $this->meta);
    }

    public function getName(): string
    {
        return "Water";
    }

    public function getBucketFillSound(): int
    {
        return LevelSoundEventPacket::SOUND_BUCKET_FILL_WATER;
    }

    public function tickRate(): int
    {
        return 1;
    }

    public function getBucketEmptySound(): int
    {
        return LevelSoundEventPacket::SOUND_BUCKET_EMPTY_WATER;
    }

    public function onUpdate($type): bool
    {
        return false;
    }

    public function onEntityCollide(Entity $entity): void
    {
        $entity->resetFallDistance();
        if ($entity->fireTicks > 0) {
            $entity->extinguish ();
        }
    }
}