<?php

namespace ImAMadDev\kit\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;

class Mage extends Kit {

	/** @var string */
	private string $name = 'Mage';

	/** @var Item */
	private Item $icon;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'mage.kit';
	
	/** @var string */
	private string $description;

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::SPIDER_EYE);
		$bow->setCustomName(TextFormat::DARK_PURPLE . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7Give negative effects to your enemies and weaken them to the maximum!"), 40) . "\n" . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	public function getSlot() : int { return 16; }
	
	public function getCooldown() : int {
		return 18000;
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
		$helmet = ItemFactory::getInstance()->get(ItemIds::GOLDEN_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::DARK_PURPLE . "Mage Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::DARK_PURPLE . "Mage Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::DARK_PURPLE . "Mage Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::GOLDEN_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::DARK_PURPLE . "Mage Boots");
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
		$sword->setCustomName(TextFormat::DARK_PURPLE . "Mage Sword");
		
		$pearls = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		
		$slowness = (ItemFactory::getInstance()->get(ItemIds::GOLDEN_NUGGET, 0, 64))->setCustomName(TextFormat::DARK_PURPLE . "Mage Slowness");
		
		$wither = (ItemFactory::getInstance()->get(ItemIds::SPIDER_EYE, 0, 64))->setCustomName(TextFormat::DARK_PURPLE . "Mage Wither");
		
		$poison = (ItemFactory::getInstance()->get(ItemIds::DYE, 2, 64))->setCustomName(TextFormat::DARK_PURPLE . "Mage Poison");
		
		$weakness = (ItemFactory::getInstance()->get(ItemIds::COAL, 0, 64))->setCustomName(TextFormat::DARK_PURPLE . "Mage Weakness");
		
		$nausea = (ItemFactory::getInstance()->get(ItemIds::SEEDS, 0, 64))->setCustomName(TextFormat::DARK_PURPLE . "Mage Nausea");
		
		$items = [$sword, $pearls, $steak, $slowness, $wither, $poison, $weakness, $nausea];
		for($i = 0; $i < 27; $i++){
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::DARK_PURPLE . TextFormat::BOLD . $this->getName());
	}

}