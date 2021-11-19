<?php

namespace ImAMadDev\block;

use pocketmine\block\Block;
use pocketmine\block\BrewingStand;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\block\tile\Tile;
use pocketmine\utils\TextFormat;
use ImAMadDev\manager\{ClaimManager, FormManager};

class PotionGenerator extends BrewingStand {
    /** @var int */
    private int $potionType;
    
    public const REGENERATION = 22;
    public const LONG_SWIFTNESS = 15;
    public const STRONG_SWIFTNESS = 16;
    public const LONG_INVISIBILITY = 8;
    public const LONG_FIRE_RESISTANCE = 13;
    public const STRONG_POISON = 25;
    public const NIGHT_VISION = 5;
    
    public const POTION_TAG = "Potion";
    /**
     * Generator constructor.
     *
     * @param int $id
     * @param Item $generatedItem
     * @param int $type
     */
    public function __construct(int $meta = 0){
		parent::__construct($meta);
		$this->potionType = self::REGENERATION;
	}

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onActivate(Item $item, Player $player = null): bool {
        $tile = $this->getWorld()->getTile($this);
        if(!$tile instanceof \ImAMadDev\tile\PotionGenerator) {
        	if($tile !== null) {
         	   $this->getWorld()->removeTile($tile);
  	      }
            /** @var CompoundTag $nbt */
            $nbt = new CompoundTag(
                "", [
                new StringTag(Tile::TAG_ID, "Generator"),
                new IntTag(Tile::TAG_X, (int)$this->x),
                new IntTag(Tile::TAG_Y, (int)$this->y),
                new IntTag(Tile::TAG_Z, (int)$this->z),
                new IntTag(self::POTION_TAG, (int)$this->getPotionType())
            ]);
            $tile = new \ImAMadDev\tile\PotionGenerator($this->getWorld(), $nbt);
            $this->getWorld()->addTile($tile);
        }
        if($item->isNull() && $player !== null) {
        	$claim = ClaimManager::getInstance()->getClaimByPosition($this->asPosition());
        	if($claim !== null) {
				if($claim->canEdit($player->getFaction())) {
					FormManager::getGeneratorForm($player, $this);
				}
			} else {
				FormManager::getGeneratorForm($player, $this);
			}
       }
        return parent::onActivate($item, $player);
    }

    /**
     * @param Item $item
     * @param Block $blockReplace
     * @param Block $blockClicked
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     *
     * @return bool
     */
    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool {
        $this->getWorld()->setBlock($this, $this, true, false);
        $tile = $this->getWorld()->getTile($this);
        $potion = $item->getNamedTag()->getInt(self::POTION_TAG, 22);
        if(!$tile instanceof \ImAMadDev\tile\PotionGenerator) {
        	if($tile !== null) {
         	   $this->getWorld()->removeTile($tile);
  	      }
            /** @var CompoundTag $nbt */
            $nbt = new CompoundTag("", [
                new StringTag(Tile::TAG_ID, "Generator"),
                new IntTag(Tile::TAG_X, (int)$this->x),
                new IntTag(Tile::TAG_Y, (int)$this->y),
                new IntTag(Tile::TAG_Z, (int)$this->z),
                new IntTag(self::POTION_TAG, (int)$potion)
            ]);
            $tile = new \ImAMadDev\tile\PotionGenerator($this->getWorld(), $nbt);
            $this->getWorld()->addTile($tile);
            $this->setPotionType($potion);
        }
        if($player !== null) {
       	 $item = $player->getInventory()->getItemInHand();
			$item->setCount($item->getCount() - 1);
			$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
		}
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onBreak(Item $item, Player $player = null): bool {
        $tile = $this->getWorld()->getTile($this);
        if($tile !== null) {
            $this->getWorld()->removeTile($tile);
        }
        return parent::onBreak($item, $player);
    }

    /**
     * @return int
     */
    public function getXpDropAmount(): int {
        return 0;
    }

    /**
     * @param Item $item
     *
     * @return Item[]
     */
    public function getDrops(Item $item): array {
        $tile = $this->getWorld()->getTile($this);
        $drop = Item::get(0);
        if($tile instanceof \ImAMadDev\tile\PotionGenerator) {
    	    $drop = Item::get($this->getItemId(), 0, 1);
      	  $drop->setCustomName(TextFormat::RESET . TextFormat::GOLD . $tile->getPotionName() . " Generator");
      	  $drop->setLore([TextFormat::LIGHT_PURPLE . "Put a chest on top of the generator to collect positions."]);
      	  $drop->setNamedTagEntry(new IntTag(self::POTION_TAG, $tile->getPotion()));
     	}
        return [$drop];
    }
    
    private function getPotionName(): string {
    	switch($this->getPotionType()){
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
    		case self::NIGHT_VISION:
        		return "Night Vision";
    		break;
    	}
    }
    
    public function getPotionByName(string $name): int {
    	switch($name){
    		case "Regeneration":
    			return self::REGENERATION;
    		break;
    		case "Long Swiftness":
        		return self::LONG_SWIFTNESS;
    		break;
    		case "Strong Swiftness":
        		return self::STRONG_SWIFTNESS;
    		break;
    		case "Long Invisibility":
        		return self::LONG_INVISIBILITY;
    		break;
    		case "Poison":
        		return self::STRONG_POISON;
    		break;
 		   case "Long Fire Resistance":
        		return self::LONG_FIRE_RESISTANCE;
    		break;
    		case "Night Vision":
        		return self::NIGHT_VISION;
    		break;
    	}
    }

    /**
     * @return Item
     */
    public function getPotionType(): int {
        return $this->potionType;
    }
    
    public function setPotionType(int $id): void{
        $this->potionType = $id;
    }
}
