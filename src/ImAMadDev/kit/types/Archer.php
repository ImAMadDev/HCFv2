<?php

namespace ImAMadDev\kit\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\color\Color;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\utils\TextFormat;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;

class Archer extends Kit {

	/** @var string */
	private string $name = 'Archer';

	/** @var Item */
	private Item $icon;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'archer.kit';
	
	/** @var string */
	private string $description;

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->setCustomName(TextFormat::YELLOW . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7Each arrow you shoot does &c15% more damage&7, so stare at your prey and shoot!"), 40) . "\n" . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	/**
	 * @return string
	 */
	public function getPermission(): string {
		return $this->permission;
	}
	
	public function getSlot() : int { return 28; }
	
	public function getCooldown() : int {
		return 18000;
	}

	/**
	 * @return array
	 */
	public function getArmor(): array {
		$helmet = ItemFactory::getInstance()->get(298, 0, 1);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomColor(new Color(255, 0, 0));
		$helmet->setCustomName(TextFormat::YELLOW . "Archer Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(299, 0, 1);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomColor(new Color(255, 0, 0));
		$chestplate->setCustomName(TextFormat::YELLOW . "Archer Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(300, 0, 1);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomColor(new Color(255, 0, 0));
		$leggings->setCustomName(TextFormat::YELLOW . "Archer Leggings");
		
		$boots = ItemFactory::getInstance()->get(301, 0, 1);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomColor(new Color(255, 0, 0));
		$boots->setCustomName(TextFormat::YELLOW . "Archer Boots");
		return [$helmet, $chestplate, $leggings, $boots];
	}

	/**
	 * @return Item
	 */
	public function getIcon(): Item {
		return $this->icon;
	}
	
	/**
	 * @return array
	 */
	public function getItems(): array {
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$sword->setCustomName(TextFormat::YELLOW . "Archer Sword");
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 2));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FLAME(), 2));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$bow->setCustomName(TextFormat::YELLOW . 'Archer Bow');
		
		
		$pearl = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		$arrow = ItemFactory::getInstance()->get(ItemIds::ARROW, 0, 1);
		
		$speed = ItemFactory::getInstance()->get(ItemIds::SUGAR, 0, 64); //AbilityManager::getInstance()->getAbilityByName('Speed_Portable')->get(64);
		
		$jump = ItemFactory::getInstance()->get(ItemIds::FEATHER, 0, 64); //AbilityManager::getInstance()->getAbilityByName('Jump_Portable')->get(64);
		
		$items = [$sword, $bow, $pearl, $steak, $speed, $jump, $arrow];
		for($i = 0; $i < 28; $i++){
			$items[] = ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22, 1);
		}
		return $items;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	#[Pure] public function isKit(string $name): bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function getDescription(): string {
		return $this->description;
	}

	public function giveKit(HCFPlayer $player) {
		foreach($this->getItems() as $item) {
			if($player->getInventory()->canAddItem($item)) {
				$player->getInventory()->addItem($item);
			} else {
				$player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
			}
		}
		foreach($this->getArmor() as $item) {
			if($player->getArmorInventory()->canAddItem($item)) {
				$player->getArmorInventory()->addItem($item);
			} else {
				$player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
			}
		}
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::YELLOW . TextFormat::BOLD . $this->getName());
	}

}