<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use ImAMadDev\customenchants\utils\Tickable;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\utils\TextFormat;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\utils\HCFUtils;
use pocketmine\item\Item;

class Overload extends CustomEnchantment implements Tickable {
	
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::NONE);
    }
    
    
    public function getId() : int {
        return CustomEnchantmentIds::OVERLOAD;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::OVERLOAD;
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
        return new EffectInstance(VanillaEffects::REGENERATION(), 60, ($level - 1));
    }
    
    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
    	return 25000;
   }

    public function isTickable(): bool
    {
        return true;
    }
   
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::OVERLOAD_ITEMS));
   }
}