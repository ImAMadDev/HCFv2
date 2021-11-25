<?php

namespace ImAMadDev\tile;

use ImAMadDev\player\HCFPlayer;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Hopper;
use pocketmine\entity\Human;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Facing;
use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\block\tile\Tile;

class PotionGenerator extends Tile {
	
	public const POTION_TAG = "Potion";
	private int $potion = 22;
	public const REGENERATION = 22;

    private int $generateTick = 0;

    public function __construct(World $world, Vector3 $pos) {
		parent::__construct($world, $pos);
        $world->scheduleDelayedBlockUpdate($pos, 25);
	}

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void {
        if($nbt->getTag(self::POTION_TAG) instanceof IntTag) {
            $this->potion = $nbt->getInt(self::POTION_TAG, self::REGENERATION);
        }
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setInt(self::POTION_TAG, $this->potion);
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function addAdditionalSpawnData(CompoundTag $nbt): void
    {
        $nbt->setTag(self::POTION_TAG, new IntTag($this->potion));
    }

    public function canUpdate(): bool
    {
        if (!$this->getPosition()->getWorld()->isChunkLoaded($this->getPosition()->getX() >> 4, $this->getPosition()->getZ() >> 4)) {
            return false;
        }

        if ($this->getPosition()->getWorld()->getNearestEntity($this->getPosition(), 25, Human::class) instanceof HCFPlayer) {
            return true;
        }
        return false;
    }

    public function onUpdate() : bool
    {
        if($this->isClosed()){
            return false;
        }
        if (!$this->canUpdate()){
            return false;
        }
        $this->timings->startTiming();
        if($this->generateTick++ >= 60) {
            $chest = $this->getBlock()->getSide(Facing::DOWN);
            $tile = $this->getPosition()->getWorld()->getTile($chest->getPosition());
            if(!$tile instanceof Chest && !$tile instanceof Hopper){
                return false;
            }
            $inventory = $tile->getInventory();
            switch ($this->getBlock()->getPotionType()){
                case 15:
                case 16:
                case 8:
                case 13:
                case 5:
                    if($inventory->canAddItem(ItemFactory::getInstance()->get(ItemIds::POTION, $this->getBlock()->getPotionType(), 1))) {
                        $inventory->addItem(ItemFactory::getInstance()->get(ItemIds::POTION, $this->getBlock()->getPotionType(), 1));
                        $this->generateTick = 0;
                    }
                    break;
                case 25:
                case 22:
                    if($inventory->canAddItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, $this->getBlock()->getPotionType(), 1))) {
                        $inventory->addItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, $this->getBlock()->getPotionType(), 1));
                        $this->generateTick = 0;
                    }
                    break;
            }
        }
        $this->timings->stopTiming();
        return true;
    }
}