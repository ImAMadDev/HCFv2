<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\customenchants\CustomEnchantments;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\command\Command;

class RenameCommand extends Command {
	
	public function __construct() {
		parent::__construct("rename", "Rename your item in hand.", "/rename (message)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(count($args) === 0) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if(!$sender->hasPermission("rename.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$item = $sender->getInventory()->getItemInHand();
		if($item->isNull()) {
			$sender->sendMessage(TextFormat::RED . "Invalid Item!");
			return;
		}
		$name = implode(" ", $args);
		$chars = str_replace(" ", "", TextFormat::clean($name));
		if(strlen($chars) > 40){
			$sender->sendMessage(TextFormat::RED . "Maximum characters 40!");
			return;
		}
		$item->setCustomName(TextFormat::colorize($name));
		$sender->getInventory()->setItemInHand($item);
		$sender->sendMessage(TextFormat::GRAY . "Your item in hand has renamed to " . TextFormat::colorize($name));
    }
}