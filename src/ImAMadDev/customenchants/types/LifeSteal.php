<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\utils\TextFormat;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\utils\HCFUtils;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class LifeSteal extends CustomEnchantment {
	
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::SWORD, ItemFlags::NONE, 2);
    }

    /**
     * @return int
     */
    public function getId() : int {
        return CustomEnchantmentIds::LIFE_STEAL;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::LIFE_STEAL;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
    	return TextFormat::RESET . TextFormat::DARK_RED . $this->getName() . " " . HCFUtils::getGreekFormat($level);
    }

    /**
     * @param int $level
     * @return EffectInstance
     */
    public function getEffectsByEnchantment(int $level = 1) : EffectInstance {
        return new EffectInstance(VanillaEffects::CONDUIT_POWER(), 60, ($level - 1));
    }
    
    public function activate(Item $item, ?int $slot = null, ?Player $player = null, int $level = 1, ?Event $event = null): void {
    	if($event instanceof EntityDamageByEntityEvent) {
    		$chance = (100 / $level);
    		if(rand(0, $chance) <= 10) {
    			$player->setHealth($player->getHealth() + 2 + $level * 1 > $player->getMaxHealth() ? $player->getMaxHealth() : $player->getHealth() + 2 + $level * 1);
    		}
    	}
   }
    
    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
    	return 35000;
   }
   
   /**
     * @return bool
     */
   public function canBeActivate(): bool {
   	return true;
   }
   
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::LIFE_STEAL_ITEMS));
   }
}