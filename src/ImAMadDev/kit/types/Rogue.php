<?php

namespace ImAMadDev\kit\types;

use JetBrains\PhpStorm\Pure;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\kit\Kit;
use ImAMadDev\manager\AbilityManager;

class Rogue extends Kit {

	/** @var string */
	private string $name = 'Rogue';

	/** @var Item */
	private Item $icon ;

	/** @var Item[] */
	private array $armor = [];

	/** @var Item[] */
	private array $items = [];
	
	/** @var string */
	private string $permission = 'rogue.kit';
	
	/** @var string */
	private string $description = "";

	public function __construct() {
		$bow = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_HELMET);
		$bow->setCustomName(TextFormat::DARK_GREEN . $this->getName());
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$this->icon = $bow;
		$this->description = wordwrap(TextFormat::colorize("&r&7You are a killer, you do more damage than you think, you just have to know how to use your power."), 40) . "\n" . TextFormat::colorize("&r&eAvailable for purchase at: &cminestalia.ml");
	}
	
	public function getSlot() : int { return 24; }
	
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
		$helmet = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::DARK_GREEN . "Rogue Helmet");
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::DARK_GREEN . "Rogue Chestplate");
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::DARK_GREEN . "Rogue Leggings");
		
		$boots = ItemFactory::getInstance()->get(ItemIds::CHAINMAIL_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->setCustomName(TextFormat::DARK_GREEN . "Rogue Boots");
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
		$sword->setCustomName(TextFormat::DARK_GREEN . "Rogue Sword");
		
		$rogue = ItemFactory::getInstance()->get(ItemIds::GOLDEN_SWORD);
		$rogue->setCustomName(TextFormat::DARK_GREEN . "Backstab Sword");
        if ($rogue instanceof Durable) $rogue->setDamage($rogue->getMaxDurability()-1);
		
		$pearl = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		$steak = ItemFactory::getInstance()->get(ItemIds::BAKED_POTATO, 0, 64);
		
		$speed = AbilityManager::getInstance()->getAbilityByName('Speed_Portable')->get(64);
		
		$jump = AbilityManager::getInstance()->getAbilityByName('Jump_Portable')->get(64);
		
		$items = [$sword, $pearl, $steak, $speed, $jump];
		for($i = 0; $i < 21; $i++){
			$items[] = ItemFactory::getInstance()->get(ItemIds::SPLASH_POTION, 22, 1);
		}
		for($i = 0; $i < 9; $i++){
			$items[] = clone $rogue;
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
		$player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::DARK_GREEN . TextFormat::BOLD . $this->getName());
	}

}