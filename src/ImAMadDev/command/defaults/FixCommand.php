<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\customenchants\CustomEnchantment;
use ImAMadDev\customenchants\CustomEnchantments;
use ImAMadDev\customenchants\types\Unrepairable;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\item\{Armor, Tool};

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\command\Command;

class FixCommand extends Command {
	
	public function __construct() {
		parent::__construct("fix", "Rename your item in hand.", "/fix [string: all] [string: player]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(count($args) === 0) {
			if(!$sender->hasPermission("fixhand.command")){
				$sender->sendMessage(TextFormat::RED."You have not permissions to use this command");
				return;
			}
			$item = $sender->getInventory()->getItemInHand();
			if($item instanceof Tool||$item instanceof Armor){
				if($item->getDamage() > 0 and !$item->hasEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::UNREPAIRABLE))){
					$sender->getInventory()->setItemInHand($item->setDamage(0));
				}
			}
			$sender->sendMessage(TextFormat::GREEN . "Item in your hand has been successfully repaired.");
		} else {
			if(($player = $this->getServer()->getPlayerByPrefix($args[0])) instanceof HCFPlayer) {
				if(!$sender->hasPermission("fixhand.other.command")){
					$sender->sendMessage(TextFormat::RED."You have not permissions to use this command");
					return;
				}
				$item = $player->getInventory()->getItemInHand();
				if($item instanceof Tool||$item instanceof Armor){
                    if($item->getDamage() > 0 and !$item->hasEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::UNREPAIRABLE))){
						$player->getInventory()->setItemInHand($item->setDamage(0));
					}
				}
				$player->sendMessage(TextFormat::GREEN . "Item in your hand has been successfully repaired.");
				$sender->sendMessage(TextFormat::GREEN . "You have repaired {$player->getName()}'s  inventory correctly."); 
			} elseif(strtolower($args[0]) === "all") {
				if(!$sender->hasPermission("fixall.command")){
					$sender->sendMessage(TextFormat::RED."You have not permissions to use this command");
					return;
				}
				if(!isset($args[1])) {
					foreach($sender->getInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
                            if($item->getDamage() > 0 and !$item->hasEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::UNREPAIRABLE))){
								$sender->getInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					foreach($sender->getArmorInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
                            if($item->getDamage() > 0 and !$item->hasEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::UNREPAIRABLE))){
								$sender->getArmorInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					$sender->sendMessage(TextFormat::GREEN . "All your inventory was successfully repaired.");
				} elseif(($player = $this->getServer()->getPlayerByPrefix($args[1])) instanceof HCFPlayer) {
					if(!$sender->hasPermission("fixall.other.command")){
						$sender->sendMessage(TextFormat::RED."You have not permissions to use this command");
						return;
					}
					foreach($sender->getInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
                            if($item->getDamage() > 0 and !$item->hasEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::UNREPAIRABLE))){
								$sender->getInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					foreach($sender->getArmorInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
                            if($item->getDamage() > 0 and !$item->hasEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::UNREPAIRABLE))){
								$sender->getArmorInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					$sender->sendMessage(TextFormat::GREEN . "You have repaired {$player->getName()}'s  inventory correctly.");
					$player->sendMessage(TextFormat::GREEN . "All your inventory was successfully repaired.");
				}
			}
		}
	}
}