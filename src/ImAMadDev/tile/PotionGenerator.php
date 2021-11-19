<?php

namespace ImAMadDev\tile;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\tile\Chest;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\tile\Hopper;

class PotionGenerator extends Tile {
	
	public const POTION_TAG = "Potion";
	public $generateTick = 0;
	private $potion = 22;
	public const REGENERATION = 22;
    public const LONG_SWIFTNESS = 15;
    public const STRONG_SWIFTNESS = 16;
    public const LONG_INVISIBILITY = 8;
    public const LONG_FIRE_RESISTANCE = 13;
    public const STRONG_POISON = 25;
    public const NIGHT_VISION = 5;
	
	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		$this->scheduleUpdate();
	}
	
	public function getNearPlayers(){
		$players = [];
		foreach($this->getWorld()->getNearbyEntities(new AxisAlignedBB($this->getFloorX() - 10, $this->getFloorY() - 10, $this->getFloorZ() - 10, $this->getFloorX() + 10, $this->getFloorY() + 10, $this->getFloorZ() + 10)) as $e){
			if($e instanceof HCFPlayer){
				$players[] = $e;
			}
		}
		return $players;
	}
	
	public function onUpdate(): bool {
		if($this->generateTick++ >= 250) {
			if($this->isClosed()) {
				return false;
			}
			$block = $this->getBlock();
			if(!$block instanceof \ImAMadDev\block\PotionGenerator) {
				return false;
			}
			/*$chest = $block->getSide(Vector3::SIDE_UP);
			$tile = $this->getWorld()->getTile($chest);*/
			$chest = $block->getSide(Vector3::SIDE_DOWN);
			$tile = $this->getWorld()->getTile($chest);
			if(!$tile instanceof Chest && !$tile instanceof Hopper){
				return true;
			}
			if(!$this->getWorld()->isChunkLoaded($this->getX() >> 4, $this->getZ() >> 4)){
				return true;
			}
			if(count($this->getNearPlayers()) < 1) {
				return true;
			}
			$inventory = $tile->getInventory();
			switch ($this->getPotion()){
				case 15:
				case 16:
				case 8:
				case 13:
				case 5:
					if($inventory->canAddItem(ItemFactory::getInstance()->get(ItemIds::POTION, $this->getPotion(), 1))) {
						$inventory->addItem(ItemFactory::getInstance()->get(ItemIds::POTION, $this->getPotion(), 1));
						$this->generateTick = 0;
					}
				break;
				case 25:
				case 22:
					if($inventory->canAddItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, $this->getPotion(), 1))) {
						$inventory->addItem(ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, $this->getPotion(), 1));
						$this->generateTick = 0;
					}
				break;
			}
			return true;
		}
		return true;
	}
	
	public function getPotion(): int {
        return $this->potion;
}

    /**
     * @param int $stack
     */
    public function setPotion(int $potion): void {
        $this->potion = $potion;
    }
    
    public function getPotionName(): string {
    	switch($this->getPotion()){
    		case self::REGENERATION:
    			return "Regeneration";
    		break;
    		case self::LONG_SWIFTNESS:
        		return "Long Swiftness";
    		break;
    		case self::STRONG_SWIFTNESS:
        		return "Strong Swiftness";
    		break;
    		case self::LONG_INVISIBILITY:
        		return "Long Invisibility";
    		break;
    		case self::STRONG_POISON:
        		return "Poison";
    		break;
    		case self::LONG_FIRE_RESISTANCE:
        		return "Long Fire Resistance";
    		break;
    		case self:: NIGHT_VISION:
        		return "Night Vision";
    		break;
    	}
    }

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void {
        if($nbt->hasTag(self::POTION_TAG)) {
            $this->potion = $nbt->getInt(self::POTION_TAG);
        }
        $this->scheduleUpdate();
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setInt(self::POTION_TAG, $this->potion);
        $this->scheduleUpdate();
    }
}