<?php

namespace ImAMadDev\kit\types;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;

class Diamond extends Kit {

	/** @var string */
	private string $name = 'Diamond';

	/** @var Item */
	private Item $icon;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'default.permission';
	
	/** @var string */
	private string $description = "";

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$bow->setCustomName(TextFormat::AQUA . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7This kit gives you armor made for brave warriors, enough to go out and fight anyone who comes through your base."), 40) . "\n" . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	public function getSlot() : int { return 22; }
	
	public function getCooldown() : int {
		return 9000;
	}
	
	/**
	 * @return string
	 */
	public function getPermission(): string {
		return $this->permission;
	}

	/**
	 * @return array
	 */
	public function getArmor(): array {
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::AQUA . "Diamond Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::AQUA . "Diamond Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::AQUA . "Diamond Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 2));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::AQUA . "Diamond Boots");
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
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::FREE_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$sword->setCustomName(TextFormat::AQUA . "Diamond Sword");
		
		$pearls = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		$inv = ItemFactory::getInstance()->get(ItemIds::POTION, 8, 1);
		$inv2 = ItemFactory::getInstance()->get(ItemIds::POTION, 8, 1);
		$speed = ItemFactory::getInstance()->get(ItemIds::POTION, 16, 1);
		$speed2 = ItemFactory::getInstance()->get(ItemIds::POTION, 16, 1);
		$speed3 = ItemFactory::getInstance()->get(ItemIds::POTION, 16, 1);
		$speed4 = ItemFactory::getInstance()->get(ItemIds::POTION, 16, 1);
		$speed5 = ItemFactory::getInstance()->get(ItemIds::POTION, 16, 1);
		$gapple = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 1);
		$apple = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 15);
		$items = [$sword, $pearls, $steak, $inv, $inv2, $speed, $speed2, $speed3, $speed4, $speed5, $gapple, $apple];
		for($i = 0; $i < 23; $i++){
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
	public function isKit(string $name): bool {
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::AQUA . TextFormat::BOLD . $this->getName());
	}

}