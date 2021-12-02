<?php

namespace ImAMadDev\manager;

use formapi\SimpleForm;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\block\PotionGenerator;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class FormManager {
	
	private static array $pots = ["Long Invisibility", "Regeneration", "Poison", "Long Fire Resistance", "Strong Swiftness", "Long Swiftness", "Night Vision"];
	
	public static function getGeneratorForm(Player $player, Block $block){
		$form = new SimpleForm(function (Player $player, $data) use($block) {
			if($data === null) return;
			if(in_array($data, self::$pots)){
				if($block instanceof PotionGenerator) {
					$block->setPotionType($block->getPotionByName($data));
					$player->sendMessage(TextFormat::GRAY . "Potion generator changed to: " . TextFormat::GOLD . $data);
				}
			}
        });
		$form->setTitle(TextFormat::GOLD . "Potions");
		foreach(self::$pots as $pot){
			$form->addButton(TextFormat::GRAY . $pot,-1, "", $pot);
		}
		$form->sendToPlayer($player);
	}
	
	public static function getBlockShopMenu(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			switch($data){
				case 0:
					self::getWoolBlockShop($player);
				break;
				case 1:
					self::getGlassBlockShop($player);
				break;
				case 2:
					self::getNetherBlockShop($player);
				break;
				case 3:
					self::getEndBlockShop($player);
				break;
				case 4:
					self::getSpecialShop($player);
				break;
				default:
				break;
				}
		});
		$form->setTitle(TextFormat::GOLD . "BLOCKS SHOP");
		$form->addButton(TextFormat::GRAY . "COLORED WOOL");
		$form->addButton(TextFormat::GRAY . "COLORED GLASS");
		$form->addButton(TextFormat::GRAY . "NETHER BLOCKS");
		$form->addButton(TextFormat::GRAY . "END BLOCKS");
		$form->addButton(TextFormat::GRAY . "SPECIALS");
		$form->sendToPlayer($player);
	}
	
	public static function getWoolBlockShop(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::WOOL, $data, 32), 500, "getWoolBlockShop");
		});
		$form->setTitle(TextFormat::GOLD . "Wool Block Shop");
		$form->addButton(TextFormat::GRAY . "White Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Orange Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Magenta Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Light Blue Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Yellow Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Lime Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Pink Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Gray Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Light Gray Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Cyan Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Purple Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Blue Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Brown Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Green Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Red Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Black Wool\n" . TextFormat::GOLD . "32 x 500$");
		$form->sendToPlayer($player);
	}
	
	public static function getGlassBlockShop(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, $data, 32), 500, "getGlassBlockShop");
		});
		$form->setTitle(TextFormat::GOLD . "Glass Block Shop");
		$form->addButton(TextFormat::GRAY . "White Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Orange Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Magenta Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Light Blue Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Yellow Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Lime Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Pink Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Gray Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Light Gray Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Cyan Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Purple Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Blue Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Brown Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Green Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Red Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Black Glass\n" . TextFormat::GOLD . "32 x 500$");
		$form->sendToPlayer($player);
	}
	
	public static function getSpecialShop(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::BREWING_STAND, $data, 1), 30000, "getSpecialShop");
		});
		$form->setTitle(TextFormat::GOLD . "Specials Block Shop");
		$form->addButton(TextFormat::GRAY . "Potion Generator\n" . TextFormat::GOLD . "1 x 30.000$");
		$form->sendToPlayer($player);
	}
	
	public static function getEndBlockShop(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			switch($data){
				case 0:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::END_STONE, 0, 32), 500, "getEndBlockShop");
				break;
				case 1:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::DRAGON_EGG, 0, 1), 500, "getEndBlockShop");
				break;
				case 2:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::END_BRICKS, 0, 32), 500, "getEndBlockShop");
				break;
				case 3:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::PURPUR_STAIRS, 0, 32), 500, "getEndBlockShop");
				break;
				case 4:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::PURPUR_BLOCK, 0, 32), 500, "getEndBlockShop");
				break;
				default:
				break;
				}
		});
		$form->setTitle(TextFormat::GOLD . "End Block Shop");
		$form->addButton(TextFormat::GRAY . "End Stone\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Dragon Egg\n" . TextFormat::GOLD . "1 x 500$");
		$form->addButton(TextFormat::GRAY . "End Bricks\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Purpur Stairs\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Purpur Block\n" . TextFormat::GOLD . "32 x 500$");
		$form->sendToPlayer($player);
	}
	
	public static function getNetherBlockShop(Player $player) : void {
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			switch($data){
				case 0:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::RED_NETHER_BRICK, 0, 32), 500, "getNetherBlockShop");
				break;
				case 1:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::NETHER_WART_BLOCK, 0, 32), 500, "getNetherBlockShop");
				break;
				case 2:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::SOUL_SAND, 0, 32), 500, "getNetherBlockShop");
				break;
				case 3:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::NETHERRACK, 0, 32), 500, "getNetherBlockShop");
				break;
				case 4:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_STAIRS, 0, 32), 500, "getNetherBlockShop");
				break;
				case 5:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_BLOCK, 0, 32), 500, "getNetherBlockShop");
				break;
				case 6:
					self::buyItem($player, ItemFactory::getInstance()->get(ItemIds::NETHER_BRICK_FENCE, 0, 32), 500, "getNetherBlockShop");
				break;
				default:
				break;
				}
		});
		$form->setTitle(TextFormat::GOLD . "Nether Block Shop");
		$form->addButton(TextFormat::GRAY . "Red Nether Brick\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Nether Wart Block\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Soul Sand\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Netherrack\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Nether Brick Stairs\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Nether Brick Block\n" . TextFormat::GOLD . "32 x 500$");
		$form->addButton(TextFormat::GRAY . "Nether Brick Fence\n" . TextFormat::GOLD . "32 x 500$");
		$form->sendToPlayer($player);
	}
	
	public static function buyItem(Player $player, Item $item, int $price, string $function) {
		if ($player instanceof HCFPlayer) {
			if($player->getBalance() >= $price){
				if($player->getInventory()->canAddItem($item)){
					$player->reduceBalance($price);
					$player->getInventory()->addItem($item);
					$player->sendMessage(TextFormat::GREEN . "Successfully purchased x". $item->getCount() ." ". $item->getName() . "!");
					self::{$function}($player);
				} else {
					$player->sendMessage(TextFormat::RED . "Your inventory is full");
				}
			} else {
				$player->sendMessage(TextFormat::RED . "You do not have enough money to make this purchase.");
			}
		}
	}
}