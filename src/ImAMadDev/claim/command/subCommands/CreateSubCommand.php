<?php

namespace ImAMadDev\claim\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;

class CreateSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("create", "/claim create (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", $args[1]);
		if(ClaimManager::getInstance()->getClaim($name) !== null || $name === "Wilderness"){
			$sender->sendMessage(TextFormat::RED . "This name can't be used");
			return;
		}
		$item = ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE, 0, 1);
		$item->setCustomName(TextFormat::RESET . TextFormat::GOLD . TextFormat::BOLD . "OP Claiming Axe");
		$item->setLore([
			"",
			TextFormat::RESET . TextFormat::GRAY . "Use this axe to select your first and second claiming position.",
			TextFormat::RESET . TextFormat::GRAY . "Left click block to set first position.",
			TextFormat::RESET . TextFormat::GRAY . "Right click block to set second position.",
			TextFormat::RESET . TextFormat::GRAY . "Sneak and click anywhere to confirm purchase of claim."
		]);
		if($sender->getInventory()->canAddItem($item)) {
			$sender->getInventory()->addItem($item);
			//$sender->setClaiming(true);
			$sender->setOpClaim(true);
			$sender->setOpClaimName($name);
			$sender->sendMessage(TextFormat::GREEN . "Now claiming {$name} land!");
		} else {
			$sender->sendMessage(TextFormat::RED . "Your inventory is too full!");
		}
    }
}