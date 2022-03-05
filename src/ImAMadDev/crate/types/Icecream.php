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
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\crate\Crate;
use ImAMadDev\customenchants\CustomEnchantments;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

class Icecream extends Crate {

	/** @var string */
	private string $name = 'Icecream';

	/** @var Item[] */
	private array $contents = [];
	
	public const ICECREAM_KEY = "IcecreamKey";

	/**
	 * @return array
	 */
	public function getInventory(): array {
		$items = [];
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$helmet->setCustomName(TextFormat::AQUA . "Icecream Helmet");
		$items[11] = $helmet;
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$chestplate->setCustomName(TextFormat::AQUA . "Icecream Chestplate");
		$items[12] = $chestplate;
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$leggings->setCustomName(TextFormat::AQUA . "Icecream Leggings");
		$items[13] = $leggings;
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 5));
        $boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::SPEED)));
		$boots->setCustomName(TextFormat::AQUA . "Icecream Boots");
		$items[14] = $boots;
		
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::FREE_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 5));
		$sword->setCustomName(TextFormat::AQUA . "Icecream Sword");
		$items[15] = $sword;
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 3));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PUNCH(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
		$bow->setCustomName(TextFormat::AQUA . 'Icecream Bow');
		$items[3] = $bow;
		
		$fortune = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE);
		$fortune->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 4));
		$fortune->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$fortune->setCustomName(TextFormat::AQUA . "Icecream Pickaxe");
		$items[5] = $fortune; 
		
		$gold_block = ItemFactory::getInstance()->get(ItemIds::GOLD_BLOCK, 0, 32);
		$iron_block = ItemFactory::getInstance()->get(ItemIds::IRON_BLOCK, 0, 32);
		$emerald_block = ItemFactory::getInstance()->get(ItemIds::EMERALD_BLOCK, 0, 16);
		$diamond_block = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BLOCK, 0, 24);
		
		$items[23] = $gold_block;
		
		$items[21] = $iron_block;
		
		$items[20] = $diamond_block;
		$items[24] = $emerald_block;
		
		$items[2] = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 3);
		
		$items[6] = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 16);
		
		return $items;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&bICECREAN CRATE");
	}

    /**
     * @param Block $block
     * @return bool
     */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::REDSTONE_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	#[Pure] public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === ItemIds::DYE && $item->getMeta() === 3 && $item->getNamedTag()->getTag(self::ICECREAM_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 3, $count);
		$item->getNamedTag()->setTag(self::KEY_TAG, new CompoundTag());
		$item->getNamedTag()->setTag(self::ICECREAM_KEY, new CompoundTag());
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::AQUA . "Icecream Crate", TextFormat::BOLD . TextFormat::AQUA . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
		$item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		return $item;
	}
	
	public function getContents(HCFPlayer|Player $player) : void {
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
                $panel->setCustomName(TextFormat::RED);
                $menu->getInventory()->setItem($i, $panel);
            }
        }
	} 

	public function open(HCFPlayer|Player $player, Block $block) : void {
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
			$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::AQUA . TextFormat::BOLD . $name);
		}
	}

}