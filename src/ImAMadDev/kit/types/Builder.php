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

class Builder extends Kit {

	/** @var string */
	private string $name = 'Builder';

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
		$bow = ItemFactory::getInstance()->get(ItemIds::GRASS);
		$bow->setCustomName(TextFormat::WHITE . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7It brings many blocks, so many that you can't even count them on the fingers of your hand."), 40);
	}
	
	public function getSlot() : int { return 14; }
	
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
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::WHITE . "Builder Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::WHITE . "Builder Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::WHITE . "Builder Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 2));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::WHITE . "Builder Boots");
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
		$sword->setCustomName(TextFormat::WHITE . "Builder Sword");
		
		
		$axe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE);
		$axe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3));
		$axe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$axe->setCustomName(TextFormat::WHITE . "Builder Axe");
		
		$pickaxe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$pickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3));
		$pickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$pickaxe->setCustomName(TextFormat::WHITE . "Builder Pickaxe");
		
		$shovel = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SHOVEL);
		$shovel->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3));
		$shovel->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$shovel->setCustomName(TextFormat::WHITE . "Builder Shovel");
		
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		
		$items = [$sword, $axe, $pickaxe, $shovel, $steak];
		$items[] = ItemFactory::getInstance()->get(ItemIds::STONE, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::STONE, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::STONE, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::STONE, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::GLASS, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::GLASS, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::GLASS, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::GLASS, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 11, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 11, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 1, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 1, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::TERRACOTTA, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::WOOD, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::WOOD, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::WOOD, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::WOOD, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::LEAVES, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::LEAVES, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::BUCKET, 10, 1);
		$items[] = ItemFactory::getInstance()->get(ItemIds::BUCKET, 8, 1);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::GRASS, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::GRASS, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::CHEST, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::CHEST, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::BRICK_BLOCK, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::BRICK_BLOCK, 0, 64);
		
		$items[] = ItemFactory::getInstance()->get(ItemIds::HOPPER, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::QUARTZ_STAIRS, 0, 64);
		$items[] = ItemFactory::getInstance()->get(ItemIds::QUARTZ_BLOCK, 0, 64);
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::WHITE . TextFormat::BOLD . $this->getName());
	}

}