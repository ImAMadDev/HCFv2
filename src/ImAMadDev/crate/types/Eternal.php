<?php

namespace ImAMadDev\crate\types;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\customenchants\CustomEnchantments;
use JetBrains\PhpStorm\Pure;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\crate\Crate;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

class Eternal extends Crate {

	/** @var string */
	private string $name = 'Eternal';

	/** @var Item[] */
	private array $contents = [];
	
	public const ETERNAL_KEY = "EternalKey";
	/**
	 * @return array
	 */
	public function getInventory(): array {
		$items = [];
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
		$helmet->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Helmet");
		$items[11] = $helmet;
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
		$chestplate->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Chestplate");
		$items[12] = $chestplate;
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
		$leggings->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Leggings");
		$items[13] = $leggings;
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 3));
		$boots->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Boots");
		$items[14] = $boots;
		
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::FREE_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$sword->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Sword");
		$items[16] = $sword;
		
		$sword2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$sword2->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Sword");
		$items[10] = $sword2;
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 3));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$bow->setCustomName(TextFormat::LIGHT_PURPLE . 'Eternal Bow');
		$items[4] = $bow;
		
		$fortune = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$fortune->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 3));
		$fortune->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $fortune->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::FORTUNE)));
		$fortune->setCustomName(TextFormat::LIGHT_PURPLE . "Eternal Pickaxe");
		$items[15] = $fortune; 
		
		$gold_block = ItemFactory::getInstance()->get(ItemIds::GOLD_BLOCK, 0, 32);
		$iron_block = ItemFactory::getInstance()->get(ItemIds::IRON_BLOCK, 0, 32);
		$emerald_block = ItemFactory::getInstance()->get(ItemIds::EMERALD_BLOCK, 0, 16);
		$diamond_block = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BLOCK, 0, 16);
		
		$items[3] = $gold_block;
		
		$items[4] = $iron_block;
		
		$items[23] = $diamond_block;
		$items[21] = $emerald_block;
		
		$items[24] = ItemFactory::getInstance()->get(ItemIds:: ENCHANTED_GOLDEN_APPLE, 0, 2);
		
		$items[20] = ItemFactory::getInstance()->get(ItemIds:: GOLDEN_APPLE, 0, 6);
		
		return $items;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&dETERNAL CRATE");
	}

    /**
     * @param Block $block
     * @return bool
     */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::LAPIS_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	#[Pure] public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === ItemIds::DYE && $item->getMeta() === 13 && $item->getNamedTag()->getTag(self::ETERNAL_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 13, $count);
		$item->getNamedTag()->setTag(self::KEY_TAG, new CompoundTag());
		$item->getNamedTag()->setTag(self::ETERNAL_KEY, new CompoundTag());
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::LIGHT_PURPLE . "Eternal Crate", TextFormat::BOLD . TextFormat::LIGHT_PURPLE . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
		$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		return $item;
	}
	
	public function getContents(HCFPlayer $player) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
		$menu->setName($this->getColoredName() . " " . TextFormat::GREEN . "Crate Content");
		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
			return $transaction->discard();
		});
		$menu->send($player);
		foreach($this->getInventory() as $slot => $item) {
			$menu->getInventory()->setItem($slot, $item);
		}
	} 

	public function open(HCFPlayer $player, Block $block) : void {
		$status = $player->getInventoryStatus(1);
		if($status === "FULL") {
			$player->sendBack($block->getPosition()->asVector3(), 1);
			return;
		} else {
			$items = [];
			foreach($this->getInventory() as $slot => $item) {
				$items[] = $item;
			}
			$win = $items[array_rand($items)];
			$name = $win->hasCustomName() === true ? $win->getCustomName() : $win->getName();
			$player->getInventory()->addItem($win);
			$item = $player->getInventory()->getItemInHand();
			$item->setCount($item->getCount() - 1);
			$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . $name);
		}
	}

}