<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use ImAMadDev\customenchants\utils\Tickable;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\utils\TextFormat;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\utils\HCFUtils;
use pocketmine\item\Item;
use pocketmine\entity\effect\EffectInstance;

class BurnShield extends CustomEnchantment implements Tickable {
	
    #[Pure] public function __construct(){
            parent::__construct($this->getName(), Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE);
    }
    
    
    public function getId() : int {
        return CustomEnchantmentIds::BURN_SHIELD;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::BURN_SHIELD;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
    	return TextFormat::RESET . TextFormat::RED . $this->getName() . " " . HCFUtils::getGreekFormat($level);
    }

    /**
     * @param int $level
     * @return EffectInstance
     */
    public function getEffectsByEnchantment(int $level = 1) : EffectInstance {
        return new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 60, ($level - 1));
    }
    
    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
    	return 25000;
   }
   
   /**
     * @return bool
     */
   public function canBeActivate(): bool {
   	return false;
   }
   
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::BURN_SHIELD_ITEMS));
   }

    public function isTickable(): bool
    {
        return true;
    }
}
