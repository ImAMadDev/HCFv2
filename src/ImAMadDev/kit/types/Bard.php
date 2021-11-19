<?php

namespace ImAMadDev\kit\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;

class Bard extends Kit {

	/** @var string */
	private string $name = 'Bard';

	/** @var Item */
	private Item $icon;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'bard.kit';
	
	/** @var string */
	private string $description = "";

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::GOLDEN_CHESTPLATE);
		$bow->setCustomName(TextFormat::GOLD . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7This kit gives positive effects to your team, you are the bonus to help them win."), 40) . "\n" . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	public function getSlot() : int { return 20; }
	
	public function getCooldown() : int {
		return 12600;
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
		$helmet->setCustomName(TextFormat::GOLD . "Bard Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::GOLDEN_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::GOLD . "Bard Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::GOLDEN_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::GOLD . "Bard Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::GOLDEN_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::GOLD . "Bard Boots");
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
		$sword->setCustomName(TextFormat::GOLD . "Bard Sword");
		
		
		$pearls = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		
		$speed = (ItemFactory::getInstance()->get(ItemIds::SUGAR, 0, 64))->setCustomName(TextFormat::GOLD . "Bard Speed");
		
		$jump = (ItemFactory::getInstance()->get(ItemIds::FEATHER, 0, 64))->setCustomName(TextFormat::GOLD . "Bard Jump Boost");
		
		$resistance = (ItemFactory::getInstance()->get(ItemIds::IRON_INGOT, 0, 64))->setCustomName(TextFormat::GOLD . "Bard Resistance");
		
		$regeneration = (ItemFactory::getInstance()->get(ItemIds::GHAST_TEAR, 0, 64))->setCustomName(TextFormat::GOLD . "Bard Regeneration");
		
		$strength = (ItemFactory::getInstance()->get(ItemIds::BLAZE_POWDER, 0, 64))->setCustomName(TextFormat::GOLD . "Bard Strength");
		
		$wither = (ItemFactory::getInstance()->get(ItemIds::SPIDER_EYE, 0, 64))->setCustomName(TextFormat::GOLD . "Bard Wither");
		
		$items = [$sword, $pearls, $steak, $speed, $jump, $resistance, $regeneration, $strength, $wither];
		for($i = 0; $i < 26; $i++){
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::GOLD . TextFormat::BOLD . $this->getName());
	}

}