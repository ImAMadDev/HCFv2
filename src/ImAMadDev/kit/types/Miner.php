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

class Miner extends Kit {

	/** @var string */
	private string $name = 'Miner';

	/** @var Item */
	private Item $icon ;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'default.permission';
	
	/** @var string */
	private string $description;

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$bow->setCustomName(TextFormat::DARK_AQUA . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7The Miner Kit offers you the basic tools to start collecting items and obtaining materials to build your base."), 40);
	}
	
	public function getSlot() : int { return 12; }
	
	public function getCooldown() : int {
		return 10800;
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
		$helmet = ItemFactory::getInstance()->get(ItemIds::IRON_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::DARK_AQUA . "Miner Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::IRON_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::DARK_AQUA . "Miner Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::IRON_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::DARK_AQUA . "Miner Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::IRON_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::DARK_AQUA . "Miner Boots");
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
		$sword->setCustomName(TextFormat::DARK_AQUA . "Miner Sword");
		
		$pickaxe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$pickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 6));
		$pickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 5));
		$pickaxe->setCustomName(TextFormat::DARK_AQUA . "Miner Pickaxe");
		
		$pick2 = clone $pickaxe;
		$pick3 = clone $pickaxe;
		$pick3->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SILK_TOUCH(), 1));
		$pale = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SHOVEL);
		$pale->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5));
		$pale->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 5));
		$pale->setCustomName(TextFormat::DARK_AQUA . "Miner Shovel");
		
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);

        return [$sword, $pickaxe, $pick2, $pick3, $pale, $steak];
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::DARK_AQUA . TextFormat::BOLD . $this->getName());
	}

}