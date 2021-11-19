<?php

namespace ImAMadDev\crate\types;

use ImAMadDev\customenchants\CustomEnchantment;
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

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\crate\Crate;
use ImAMadDev\customenchants\CustomEnchantments;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;


class KOTH extends Crate {

	/** @var string */
	private string $name = 'KOTH';

	/** @var Item[] */
	private array $contents = [];
	
	public const KOTH_KEY = "KOTHKey";
	
	/**
	 * @return array
	 */
	public function getInventory(): array {
		$items = [];
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::KOTH_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$helmet->setCustomName(TextFormat::DARK_RED . "KOTH Helmet");
		$items[3] = $helmet;
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$chestplate->setCustomName(TextFormat::DARK_RED . "KOTH Chestplate");
		$items[4] = $chestplate;
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::KOTH_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$leggings->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::IMPLANTS), 1));
		$leggings->setCustomName(TextFormat::DARK_RED . "KOTH Leggings");
		$items[5] = $leggings;
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 4));
        $boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::SPEED), 2));
		$boots->setCustomName(TextFormat::DARK_RED . "KOTH Boots");
		$items[6] = $boots;
		
		$helmet2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$helmet2->setCustomName(TextFormat::DARK_RED . "KOTH Helmet");
		$items[12] = $helmet2;
		
		$chestplate2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::KOTH_PROTECTION));
		$chestplate2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$chestplate2->setCustomName(TextFormat::DARK_RED . "KOTH Chestplate");
		$items[13] = $chestplate2;
		
		$leggings2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
        $leggings2->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::IMPLANTS), 1));
		$leggings2->setCustomName(TextFormat::DARK_RED . "KOTH Leggings");
		$items[14] = $leggings2;
		
		$boots2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::KOTH_PROTECTION));
		$boots2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$boots2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 5));
        $boots2->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::SPEED), 2));
		$boots2->setCustomName(TextFormat::DARK_RED . "KOTH Boots");
		$items[15] = $boots2;
		
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::FREE_SHARPNESS));
		$sword->setCustomName(TextFormat::DARK_RED . "KOTH Fire");
		$items[1] = $sword;
		
		$sword2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::KOTH_SHARPNESS));
		$sword2->setCustomName(TextFormat::DARK_RED . "KOTH Sharpness");
		$items[10] = $sword2;
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 4));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FLAME(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
		$bow->setCustomName(TextFormat::DARK_RED . 'KOTH Bow');
		$items[23] = $bow;
		
		$fortune = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$fortune->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 5));
		$fortune->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
        $fortune->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::FORTUNE), 5));
		$fortune->setCustomName(TextFormat::DARK_RED . "KOTH Fortune");
		$items[2] = $fortune; 
		
		$touch = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$touch->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4));
		$touch->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$touch->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SILK_TOUCH(), 1));
		$touch->setCustomName(TextFormat::DARK_RED . "KOTH Silk Touch");
		$items[11] = $touch; 
		
		$gold_block = ItemFactory::getInstance()->get(BlockLegacyIds::GOLD_BLOCK, 0, 32);
		$iron_block = ItemFactory::getInstance()->get(BlockLegacyIds::IRON_BLOCK, 0, 32);
		$emerald_block = ItemFactory::getInstance()->get(BlockLegacyIds::EMERALD_BLOCK, 0, 32);
		$diamond_block = ItemFactory::getInstance()->get(BlockLegacyIds::DIAMOND_BLOCK, 0, 32);
		
		$items[19] = $gold_block;
		
		$items[20] = $iron_block;
		
		$items[24] = $diamond_block;
		$items[25] = $emerald_block;
		
		$items[7] = ItemFactory::getInstance()->get(ItemIds:: ENCHANTED_GOLDEN_APPLE, 0, 3);
		
		$items[16] = ItemFactory::getInstance()->get(ItemIds:: GOLDEN_APPLE, 0, 7);
		
		$items[21] = ItemFactory::getInstance()->get(ItemIds:: ENDER_PEARL, 0, 16);
		
		return $items;
	}
	
	public function addCustomEnchantment(Item $item, string $name, int $level) : Item {
		$enchantment = CustomEnchantments::getEnchantmentByName($name);
		$item->addEnchantment(new EnchantmentInstance($enchantment, $level));
		$newLore = $item->getLore();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        array_unshift($newLore, $enchantment->getNameWithFormat($level));
		$item->setLore($newLore);
		return $item;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&4KOTH CRATE");
	}

    /**
     * @param Block $block
     * @return bool
     */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::IRON_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	#[Pure] public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === ItemIds::DYE && $item->getMeta() === 1 && $item->getNamedTag()->getTag(self::KOTH_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 1, $count);
        $item->getNamedTag()->setTag(self::KEY_TAG, CompoundTag::create());
		$item->getNamedTag()->setTag(self::KOTH_KEY, CompoundTag::create());
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::DARK_RED . "KOTH Crate", TextFormat::BOLD . TextFormat::DARK_RED . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
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
        for ($i = 0; $i < $menu->getInventory()->getSize(); $i++){
            if ($menu->getInventory()->getItem($i)->getId() == BlockLegacyIds::AIR){
                $panel = ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS_PANE, 14);
                $panel->setCustomName(TextFormat::RED . "");
                $menu->getInventory()->setItem($i, $panel);
            }
        }
	} 

	public function open(HCFPlayer $player, Block $block) : void {
		$status = $player->getInventoryStatus();
		if($status === "FULL") {
			$player->sendBack($block->getPosition()->asVector3(), 1);
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
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::DARK_RED . TextFormat::BOLD . $name);
		}
	}

}