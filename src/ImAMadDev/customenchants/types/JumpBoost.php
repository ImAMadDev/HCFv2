<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\utils\TextFormat;

use ImAMadDev\utils\HCFUtils;
use ImAMadDev\customenchants\CustomEnchantment;

use pocketmine\item\Item;

class JumpBoost extends CustomEnchantment {

    /**
     * JumpBoost Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::COMMON, ItemFlags::ARMOR, ItemFlags::NONE, 2);
    }

    /**
     * @return int
     */
    public function getId() : int {
        return CustomEnchantmentIds::JUMP_BOOST;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::JUMP_BOOST;
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
        return new EffectInstance(VanillaEffects::JUMP_BOOST(), 60, ($level - 1));
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
   	 return (in_array($item->getId(), CustomEnchantment::JUMP_BOOST_ITEMS));
   }
}