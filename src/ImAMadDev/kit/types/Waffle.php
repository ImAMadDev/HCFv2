<?php

namespace ImAMadDev\kit\types;

use ImAMadDev\customenchants\CustomEnchantment;
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
use ImAMadDev\customenchants\CustomEnchantments;

class Waffle extends Kit {

	/** @var string */
	private string $name = 'Waffle';

	/** @var Item */
	private Item $icon;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'waffle.kit';

    private string $description;

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::NETHERSTAR);
		$bow->setCustomName(TextFormat::MINECOIN_GOLD . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7This kit is worthy for those who send death to their enemy, with this no one will be your rival."), 40) . "\n " . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	public function getSlot() : int { return 40; }
	
	public function getCooldown() : int {
		return 259200;
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
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 8));
        $helmet->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
        $helmet->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::INVISIBILITY)));
		$helmet->setCustomName(TextFormat::MINECOIN_GOLD . "Waffle Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::KOTH_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 8));
        $chestplate->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
        $chestplate->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::BURN_SHIELD)));
		$chestplate->setCustomName(TextFormat::MINECOIN_GOLD . "Waffle Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 8));
        $leggings->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
        $leggings->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::IMPLANTS)));
		$leggings->setCustomName(TextFormat::MINECOIN_GOLD . "Waffle Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 10));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 8));
        $boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
        $boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::SPEED), 2));
        $boots->setCustomName(TextFormat::MINECOIN_GOLD . "Waffle Boots");
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
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::KOTH_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 8));
        $sword->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::NUTRITION)));
		$sword->setCustomName(TextFormat::MINECOIN_GOLD . "Waffle Sword");
		
		$pearls = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$pearls2 = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$pearls3 = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		$apple = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 64);
		$gapple = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 10);
		//Combo Ability
		$keys = CrateManager::getInstance()->getCrateByName('Waffle')->getCrateKey();

		$items = [$sword, $pearls, $pearls2, $pearls3, $steak, $apple, $gapple, $keys];
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::MINECOIN_GOLD . TextFormat::BOLD . $this->getName());
	}

}