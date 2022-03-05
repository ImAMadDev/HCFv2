<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use ImAMadDev\customenchants\utils\Actionable;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

use ImAMadDev\utils\HCFUtils;
use ImAMadDev\customenchants\CustomEnchantment;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\block\BlockLegacyIds;
use pocketmine\player\Player;

class Smelting extends CustomEnchantment implements Actionable {

    /**
     * SmeltingEnchantment Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::DIG, ItemFlags::PICKAXE);
    }

    /**
     * @return int
     */
    public function getId() : int {
        return CustomEnchantmentIds::SMELTING;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::SMELTING;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
    	return TextFormat::RESET . TextFormat::GREEN . $this->getName() . " " . HCFUtils::getGreekFormat($level);
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
    		switch($event->getBlock()->getId()) {
    			case BlockLegacyIds::GOLD_ORE:
					$event->setDrops([ItemFactory::getInstance()->get(ItemIds::GOLD_INGOT, 0, 1)]);
				break;
				case BlockLegacyIds::IRON_ORE:
					$event->setDrops([ItemFactory::getInstance()->get(ItemIds::IRON_INGOT, 0, 1)]);
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
   	 return (in_array($item->getId(), CustomEnchantment::SMELTING_ITEMS));
   }
}