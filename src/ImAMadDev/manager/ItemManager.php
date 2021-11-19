<?php

namespace ImAMadDev\manager;

use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\item\ItemFactory;

use ImAMadDev\item\{EnderPearl, FishingRod, EnchantedBook, GoldenApple, GoldenAppleEnchanted, Fireworks, SplashPotion, EnderEye};
use ImAMadDev\HCF;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\item\PotionType;

class ItemManager {
	
	public static ?HCF $main = null;
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->init();
	}
	
	public function init() {
        $if = ItemFactory::getInstance();
		$if->register(new EnderPearl(), true);
        //$if->register(new FishingRod(), true);
        foreach(PotionType::getAll() as $type){
            $typeId = PotionTypeIdMap::getInstance()->toId($type);
            $if->register(new Potion(new ItemIdentifier(ItemIds::POTION, $typeId), $type->getDisplayName() . " Potion", $type), true);
            $if->register(new SplashPotion(new ItemIdentifier(ItemIds::SPLASH_POTION, $typeId), $type->getDisplayName() . " Splash Potion", $type), true);
        }
        $if->register(new GoldenApple(new ItemIdentifier(ItemIds::GOLDEN_APPLE, 0), "Golden Apple"), true);
        $if->register(new GoldenAppleEnchanted(new ItemIdentifier(ItemIds::ENCHANTED_GOLDEN_APPLE,0), "Enchanted Golden Apple"), true);
        $if->register(new EnderEye(), true);
        $if->register(new EnchantedBook(), true);
        $if->register(new Fireworks(new ItemIdentifier(ItemIds::FIREWORKS, 0), "Fireworks"), true);
    }
}