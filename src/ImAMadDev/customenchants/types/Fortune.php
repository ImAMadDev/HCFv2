<?php

namespace ImAMadDev\customenchants\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\utils\HCFUtils;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;

class Fortune extends CustomEnchantment {

    /**
     * FortuneEnchantment Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::DIG, ItemFlags::PICKAXE, 3);
    }

    /**
     * @return int
     */
    public function getId() : string {
        return EnchantmentIds::FORTUNE;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::FORTUNE;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
    	return TextFormat::RESET . TextFormat::GOLD."Fortune " . HCFUtils::getGreekFormat($level);
    }

    /**
     * @param int $level
     * @return EffectInstance
     */
    public function getEffectsByEnchantment(int $level = 1) : EffectInstance {
        return new EffectInstance(VanillaEffects::CONDUIT_POWER(), 60, ($level - 1));
    }
    
    public function activate(Item $item, ?int $slot = null, ?Player $player = null, int $level = 1, ?Event $event = null): void {
    	if($event instanceof BlockBreakEvent) {
  	  	$rand = rand(1, ($level + 1));
    		switch($event->getBlock()->getId()) {
    			case 16:
					$event->setDrops([ItemFactory::getInstance()->get(ItemIds::COAL, 0, 1 + $rand)]);
				break;
				case 21:
					if($item->getId() !== 270) {
						$event->setDrops([ItemFactory::getInstance()->get(ItemIds::DYE, 4, rand(1, 4) + $rand)]);
					}
				break;
                case 74:
                case 73:
					if($item->getId() !== 270) {
						$event->setDrops([ItemFactory::getInstance()->get(ItemIds::REDSTONE, 0, rand(2, 3) + $rand)]);
					}
				break;
                case 153:
					if($item->getId() !== 270) {
						$event->setDrops([ItemFactory::getInstance()->get(153, 0, rand(1, 2) + $rand)]);
					}
				break;
				case 56:
					if(!in_array($item->getId(), [270, 274, 285])) {
						$event->setDrops([ItemFactory::getInstance()->get(ItemIds::DIAMOND, 0, 1 + $rand)]);
					}
				break;
				case 129:
					if(!in_array($item->getId(), [270, 274, 285])) {
						$event->setDrops([ItemFactory::getInstance()->get(388, 0, 1 + $rand)]);
					}
				break;
				case 18: # Leaves
					if(rand(1, 100) <= 10 + $level * 2) {
						$event->setDrops([ItemFactory::getInstance()->get(260, 0, 1)]);
					}
				break;
    		}
    	}
   }
    
    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
    	return 5000;
   }
   
   /**
     * @return bool
     */
   public function canBeActivate(): bool {
   	return true;
   }
   
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::FORTUNE_ITEMS));
   }
}