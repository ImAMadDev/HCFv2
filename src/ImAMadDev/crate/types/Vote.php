<?php

namespace ImAMadDev\crate\types;

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
use ImAMadDev\crate\Crate;
use ImAMadDev\manager\{CrateManager, AbilityManager};

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

class Vote extends Crate {

	/** @var string */
	private string $name = 'Vote';

	/** @var Item[] */
	private array $contents = [];
	
	public const VOTE_KEY = "VoteKey";
	
	/**
	 * @return array
	 */
	public function getInventory(): array {
		$items = [];
		
		$diamond_block = ItemFactory::getInstance()->get(BlockLegacyIds::DIAMOND_BLOCK, 0, 32);
		$emerald_block = ItemFactory::getInstance()->get(BlockLegacyIds::EMERALD_BLOCK, 0, 32);
		$iron_block = ItemFactory::getInstance()->get(BlockLegacyIds::IRON_BLOCK, 0, 32);
		
		$items[14] = AbilityManager::getInstance()->getAbilityByName('Strength_Portable')?->get(rand(1, 2));
		$items[12] = AbilityManager::getInstance()->getAbilityByName('Resistance_Portable')?->get(rand(1, 2));
		
		$items[3] = CrateManager::getInstance()->getCrateByName('Cupcacke')?->getCrateKey();
		$items[5] = CrateManager::getInstance()->getCrateByName('Icecream')?->getCrateKey();
		$items[22] = CrateManager::getInstance()->getCrateByName('Basic')?->getCrateKey(rand(1, 3));
		
		$items[11] = $iron_block;
		$items[13] = $emerald_block;
		$items[15] = $diamond_block;
		
		$items[23] = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_GOLDEN_APPLE, 0, 3);
		$items[21] = ItemFactory::getInstance()->get(ItemIds::GOLDEN_APPLE, 0, 10);
		$items[4] = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 16);
		
		return $items;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
	
	public function getColoredName() : string {
		return TextFormat::colorize("&7VOTE CRATE");
	}

    /**
     * @param Block $block
     * @return bool
     */
	public function isCrate(Block $block): bool {
		if(in_array($block->getId(), self::BLOCK_CRATES)) {
			if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::EMERALD_BLOCK){
				return true;
			}
		}
		return false;
	}
	
	#[Pure] public function isCrateName(string $name) : bool {
		return strtolower($this->getName()) == strtolower($name);
	}
	
	public function isCrateKey(Item $item) : bool {
		if($item->getId() === ItemIds::DYE && $item->getMeta() === 7 && $item->getNamedTag()->getTag(self::VOTE_KEY) instanceof CompoundTag) {
			return true;
		}
		return false;
	}
	
	public function getCrateKey(int $count = 1): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::DYE, 7, $count);
        $item->getNamedTag()->setTag(self::KEY_TAG, CompoundTag::create());
		$item->getNamedTag()->setTag(self::VOTE_KEY, CompoundTag::create());
		$item->setCustomName($this->getColoredName() . " KEY");
		$item->setLore([TextFormat::GRAY . "You can redeem this key at " . TextFormat::YELLOW . "Vote Crate", TextFormat::BOLD . TextFormat::YELLOW . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
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
                $panel->setCustomName(TextFormat::RED . " ");
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
			$player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GRAY . TextFormat::BOLD . $name);
		}
	}

}