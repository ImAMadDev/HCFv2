<?php

namespace ImAMadDev\customenchants\types;

use ImAMadDev\customenchants\CustomEnchantmentIds;
use ImAMadDev\customenchants\utils\Actionable;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\ItemFlags;
use pocketmine\item\enchantment\Rarity;
use pocketmine\utils\TextFormat;

use ImAMadDev\specials\types\Key;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\customenchants\CustomEnchantment;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;

class KeyFinder extends CustomEnchantment implements Actionable {

    /**
     * KeyFinderEnchantment Constructor.
     */
    #[Pure] public function __construct(){
        parent::__construct($this->getName(), Rarity::MYTHIC, ItemFlags::DIG, ItemFlags::PICKAXE);
    }

    /**
     * @return int
     */
    public function getId() : int {
        return CustomEnchantmentIds::KEY_FINDER;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return CustomEnchantment::KEY_FINDER;
    }

    /**
     * @param int $level
     * @return string
     */
    #[Pure] public function getNameWithFormat(int $level = 1) : string {
    	return TextFormat::RESET . TextFormat::LIGHT_PURPLE . $this->getName() . " " . HCFUtils::getGreekFormat($level);
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
    		$probability = (500 / $level);
    		if(rand(0, $probability) === rand(0, $probability)) {
    			$keys = ["legendary", "ability", "zkita", "ejrecords"];
          	    new Key($player, $keys[array_rand($keys)], (2 *$level));
          	    $player->sendMessage(TextFormat::colorize("&dKey Finder") . TextFormat::GOLD . " You have found a key!");
       	    }
    	}
   }
    
    /**
     * @return int
     */
    public function getEnchantmentPrice() : int {
    	return 100000;
   }
   
   /**
     * @return bool
     */
   public function canBeActivate(): bool {
   	return true;
   }
   
   public function canEnchant(Item $item): bool {
   	 return (in_array($item->getId(), CustomEnchantment::KEY_FINDER_ITEMS));
   }
}