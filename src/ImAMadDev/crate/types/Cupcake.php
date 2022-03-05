<?php

namespace ImAMadDev\crate\types;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\customenchants\CustomEnchantments;
use JetBrains\PhpStorm\Pure;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\enchantment\{EnchantmentInstance, VanillaEnchantments};
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\crate\Crate;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

class Cupcake extends Crate {

	/** @var string */
	private string $name = 'Cupcake';

	/** @var Item[] */
	private array $contents = [];
	
	public const CUPCAKE_KEY = "CupcakeKey";

	/**
	 * @return array
	 */
	public function getInventory(): array {
		$items = [];
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
        $helmet->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
		$helmet->setCustomName(TextFormat::LIGHT_PURPLE . "Cupcake Helmet");
		$items[11] = $helmet;
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
        $chestplate->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
		$chestplate->setCustomName(TextFormat::LIGHT_PURPLE . "Cupcake Chestplate");
		$items[12] = $chestplate;
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_PROTECTION(), HCFUtils::PAID_PROTECTION));
        $leggings->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
        $leggings->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::IMPLANTS)));
		$leggings->setCustomName(TextFormat::LIGHT_PURPLE . "Cupcake Leggings");
		$items[13] = $leggings;
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::PAID_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 5));
        $boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::SPEED), 2));
        $boots->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::HELL_FORGET)));
		$boots->setCustomName(TextFormat::LIGHT_PURPLE . "Cupcake Boots");
		$items[14] = $boots;
		
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::PAID_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 6));
        $helmet->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::NUTRITION)));
		$sword->setCustomName(TextFormat::LIGHT_PURPLE . "Cupcake Sword");
		$items[2] = $sword;
		
		$sword2 = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::PAID_SHARPNESS));
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), HCFUtils::KOTH_SHARPNESS));
		$sword2->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3));
        $sword2->addEnchantment(new EnchantmentInstance(CustomEnchantments::getEnchantmentByName(CustomEnchantment::NUTRITION), 2));
		$sword2->setCustomName(TextFormat::LIGHT_PURPLE . "Cupcake Sword");
		$items[6] = $sword2;
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 4));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PUNCH(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FLAME(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 2));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 4));
		$bow->setCustomName(TextFormat::LIGHT_PURPLE . 'Cupcake Bow');
		$items[15] = $bow;
		
		$gold_block = ItemFactory::getInstance()->get(ItemIds::GOLD_BLOCK, 0, 48);
		$iron_block = ItemFactory::getInstance()->get(ItemIds::IRON_BLOCK, 0, 48);
		$emerald_block = ItemFactory::getInstance()->get(ItemIds::EMERALD_BLOCK, 0, 24);
		$diamond_block = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BLOCK, 0, 32);
		
		$items[21] = $gold_block;
		
		$items[22] = $iron_block;
		
		$items[23] = $diamond_block;
		$items[3] = $emerald_block;
		
		$items[24] = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 5);
		
		$items[20] = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 32);
		
		$items[5] = ItemFactory::getInstance()->get(ItemIds::COBWEB, 0, 16);
		
		return $items;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&dCUPCAKE CRATE");
	}

    /**
     * @param Block $block
     * @return bool
     */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::DIAMOND_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	#[Pure] public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === ItemIds::DYE  && $item->getMeta() === 2 && $item->getNamedTag()->getTag(self::CUPCAKE_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 2, $count);
		$item->getNamedTag()->setTag(self::KEY_TAG, new CompoundTag());
		$item->getNamedTag()->setTag(self::CUPCAKE_KEY, new CompoundTag());
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::LIGHT_PURPLE . "Cupcake Crate", TextFormat::BOLD . TextFormat::LIGHT_PURPLE . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
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
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . $name);
		}
	}

}