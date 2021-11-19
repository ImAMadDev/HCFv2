<?php

namespace ImAMadDev\kit\types;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\customenchants\CustomEnchantments;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\utils\TextFormat;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;

class Eternal extends Kit {

	/** @var string */
	private string $name = 'Eternal';

	/** @var Item */
	private ?Item $icon = null;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'eternal.kit';
	
	/** @var string */
	private string $description = "";

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::ENDER_EYE);
		$bow->setCustomName(TextFormat::LIGHT_PURPLE . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7An eternal rank for eternal people, you are tough enough to keep up in a PVP."), 40) . "\n" . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	public function getSlot() : int { return 30; }
	
	public function getCooldown() : int {
		return 172800;
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
		$helmet->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $chestplate->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
		$chestplate->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Helmet");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
		$leggings->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Helmet");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::SPEED)));
		$boots->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Helmet");
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
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::FREE_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
		$sword->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Sword");
		
		$pearl = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$pearl2 = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		
		$eggs = ItemFactory::getInstance()->get(ItemIds::EGG, 0, 16);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		
		$apple = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 16);
		
		$gapple = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 3);
		
		$inv = ItemFactory::getInstance()->get(ItemIds::POTION, 8, 1);
		$inv2 = ItemFactory::getInstance()->get(ItemIds::POTION, 8, 1);
		$inv3 = ItemFactory::getInstance()->get(ItemIds::POTION, 8, 1);
		
		$items = [$sword, $pearl, $pearl2, $steak, $eggs, $apple, $gapple, $inv, $inv2, $inv3];
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

	public function giveKit(HCFPlayer $player) : void
    {
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . $this->getName());
	}

}