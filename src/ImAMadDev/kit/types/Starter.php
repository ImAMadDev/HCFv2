<?php

namespace ImAMadDev\kit\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;
use ImAMadDev\manager\CrateManager;

class Starter extends Kit {

	/** @var string */
	private string $name = 'Starter';

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
		$bow = ItemFactory::getInstance()->get(ItemIds::LEATHER_CHESTPLATE);
		$bow->setCustomName(TextFormat::GREEN . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7With this kit you can start playing HCF, it has the basics to get you started, but beware of getting into a &cPVP&7!"), 40);
	}

    /**
     * @return int
     */
	public function getSlot() : int { return 10; }

    /**
     * @return int
     */
	public function getCooldown() : int {
		return 14400;
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
		$helmet = ItemFactory::getInstance()->get(ItemIds::LEATHER_CAP);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::GREEN . "Starter Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::LEATHER_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::GREEN . "Starter Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::LEATHER_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::GREEN . "Starter Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::LEATHER_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::GREEN . "Starter Boots");
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
		$sword = ItemFactory::getInstance()->get(ItemIds::IRON_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1));
		$sword->setCustomName(TextFormat::GREEN . "Starter Sword");
		
		$axe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE);
		$axe->setCustomName(TextFormat::GREEN . "Starter Axe");
		$axe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));
		
		$pickaxe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$pickaxe->setCustomName(TextFormat::GREEN . "Starter Pickaxe");
		$pickaxe->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));
		
		$shovel = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SHOVEL);
		$shovel->setCustomName(TextFormat::GREEN . "Starter Shovel");
		$shovel->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2));
		$keys = CrateManager::getInstance()->getCrateByName('Basic')->getCrateKey(5);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
        return [$sword, $steak, $axe, $pickaxe, $shovel, $keys];
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

    /**
     * @return string
     */
	public function getDescription(): string {
		return $this->description;
	}

    /**
     * @param HCFPlayer $player
     * @return void
     */
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::GREEN . TextFormat::BOLD . $this->getName());
	}

}