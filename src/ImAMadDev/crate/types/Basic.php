<?php

namespace ImAMadDev\crate\types;

use JetBrains\PhpStorm\Pure;
use muqsit\invmenu\MenuIds;
use muqsit\invmenu\type\InvMenuTypeIds;
use muqsit\invmenu\type\util\InvMenuTypeBuilders;
use pocketmine\block\BlockIds;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\StringTag;
use pocketmine\item\enchantment\{Enchantment, EnchantmentInstance, VanillaEnchantments};
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\crate\Crate;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

use function count;

class Basic extends Crate {

	/** @var string */
	private string $name = 'Basic';

	/** @var Item[] */
	private array $contents = [];
	
	public const BASIC_KEY = "BasicKey";
	
	/**
	 * @return array
	 */
	public function getInventory(): array {
		$items = [];
		$helmet = ItemFactory::getInstance()->get(ItemIds::DIAMOND_HELMET);
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$helmet->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$helmet->setCustomName(TextFormat::YELLOW . "Basic Helmet");
		$items[10] = $helmet;
		
		$chestplate = ItemFactory::getInstance()->get(ItemIds::DIAMOND_CHESTPLATE);
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$chestplate->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$chestplate->setCustomName(TextFormat::YELLOW . "Basic Chestplate");
		$items[19] = $chestplate;
		
		$leggings = ItemFactory::getInstance()->get(ItemIds::DIAMOND_LEGGINGS);
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$leggings->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$leggings->setCustomName(TextFormat::YELLOW . "Basic Leggings");
		$items[11] = $leggings;
		
		$boots = ItemFactory::getInstance()->get(ItemIds::DIAMOND_BOOTS);
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), HCFUtils::FREE_PROTECTION));
		$boots->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$boots->setCustomName(TextFormat::YELLOW . "Basic Boots");
		$items[20] = $boots;
		
		$sword = ItemFactory::getInstance()->get(ItemIds::DIAMOND_SWORD);
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), HCFUtils::FREE_SHARPNESS));
		$sword->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
		$sword->setCustomName(TextFormat::YELLOW . "Basic Sword");
		$items[13] = $sword;
		
		$bow = ItemFactory::getInstance()->get(ItemIds::BOW);
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 1));
		$bow->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
		$bow->setCustomName(TextFormat::YELLOW . 'Basic Bow');
		$items[22] = $bow;
		
		$gold_block = ItemFactory::getInstance()->get(BlockLegacyIds::GOLD_BLOCK, 0, 10);
		$iron_block = ItemFactory::getInstance()->get(BlockLegacyIds::IRON_BLOCK, 0, 10);
		$items[15] = $gold_block;
		
		$items[16] = $iron_block;
		
		$items[25] = $gold_block;
		$items[24] = $iron_block;
		
		return $items;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&eBASIC CRATE");
	}

    /**
     * @param Block $block
     * @return bool
     */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::COAL_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	#[Pure] public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === ItemIds::DYE && $item->getMeta() === 11 && $item->getNamedTag()->getTag(self::BASIC_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 11, $count);
        $item->getNamedTag()->setTag(self::KEY_TAG, CompoundTag::create());
        $item->getNamedTag()->setTag(self::BASIC_KEY, CompoundTag::create());
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::YELLOW . "Basic Crate", TextFormat::BOLD . TextFormat::YELLOW . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
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
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GREEN . TextFormat::BOLD . $name);
		}
	}

}