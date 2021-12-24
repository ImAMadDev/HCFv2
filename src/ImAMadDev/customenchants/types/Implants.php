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
use pocketmine\player\Player;

class Implants extends CustomEnchantment implements Tickable {
	
	
	#[Pure] public function __construct(){
		parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::NONE, 2);
	}
	
	
	public function getId() : int {
		return CustomEnchantmentIds::IMPLANTS;
	}
	
	public function getName() : string {
		return CustomEnchantment::IMPLANTS;
	}
	
	#[Pure] public function getNameWithFormat(int $level = 1) : string {
		return TextFormat::RESET . TextFormat::DARK_RED . $this->getName() . " " . HCFUtils::getGreekFormat($level);
	}
	
	public function getEffectsByEnchantment(int $level = 1) : EffectInstance {
		return new EffectInstance(VanillaEffects::SATURATION(), 60, ($level - 1));
	}
	
    public function getEnchantmentPrice() : int {
    	return 30000;
   }

    /**
     * @param Item $item
     * @return bool
     */
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::IMPLANTS_ITEMS));
   }

    public function isTickable(): bool
    {
        return true;
    }
}