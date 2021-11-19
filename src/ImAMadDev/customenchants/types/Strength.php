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

use pocketmine\item\Item;
class Strength extends CustomEnchantment {

    /**
     * StrengthEnchantment Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE);
    }

    /**
     * @return int
     */
    public function getId() : int {
        return CustomEnchantmentIds::STRENGTH;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::STRENGTH;
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
        return new EffectInstance(VanillaEffects::STRENGTH(), 60, ($level - 1));
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
   	 return (in_array($item->getId(), CustomEnchantment::STRENGTH_ITEMS));
   }
}