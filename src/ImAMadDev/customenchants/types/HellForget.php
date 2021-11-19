<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ImAMadDev\customenchants\CustomEnchantment;
use pocketmine\entity\Effect\EffectInstance;
use pocketmine\item\Item;

class HellForget extends CustomEnchantment {
	
	#[Pure] public function __construct(){
		parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::ARMOR, ItemFlags::NONE);
	}
	
	public function getId() : int {
		return CustomEnchantmentIds::HELL_FORGET;
	}

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::HELL_FORGET;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
    	return TextFormat::RESET . TextFormat::DARK_RED . $this->getName();
    }

    /**
     * @param int $level
     * @return EffectInstance
     */
    public function getEffectsByEnchantment(int $level = 1) : EffectInstance {
        return new EffectInstance(VanillaEffects::CONDUIT_POWER(), 60, ($level - 1));
    }
    
    public function activate(Item $item, ?int $slot = null, ?Player $player = null, int $level = 1): void {
        if ($item instanceof Armor) {
            if ($item->getDamage() > 0) {
                if (($item->getDamage() - $this->getMaxLevel()) < 0) {
                    $item->setDamage(0);
                } else {
                    $item->setDamage($item->getDamage() - $this->getMaxLevel());
                }
                $player->getArmorInventory()->setItem($slot, $item);
            }
        }
   }
    
    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
    	return 30000;
   }
   
   /**
     * @return bool
     */
   public function canBeActivate(): bool {
   	return true;
   }
   
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::HELL_FORGET_ITEMS));
   }
}