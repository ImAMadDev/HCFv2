<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\{EOTWManager, ClaimManager};

use ImAMadDev\player\PlayerUtils;
use ImAMadDev\player\sessions\ClaimSession;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class ClaimSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("claim", "/faction claim");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(EOTWManager::isEnabled() === true) {
			$sender->sendMessage(TextFormat::RED . "You cannot do this because End Of The World is enabled");
			return;
		}
		$faction = $sender->getFaction();
		if($faction === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
			return;
		}
		if(!$sender->getFaction()->isLeader($sender->getName())) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(ClaimManager::getInstance()->getClaim($faction->getName()) !== null){
			$sender->sendMessage(TextFormat::RED . "You already have an existing claim! You must unclaim your current claim!");
			return;
		}
		$item = ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE, 0, 1);
		$item->setCustomName(TextFormat::RESET . TextFormat::GOLD . TextFormat::BOLD . "Claiming Axe");
		$item->setLore([
			"",
			TextFormat::RESET . TextFormat::GRAY . "Use this axe to select your first and second claiming position.",
			TextFormat::RESET . TextFormat::GRAY . "Left click block to set first position.",
			TextFormat::RESET . TextFormat::GRAY . "Right click block to set second position.",
			TextFormat::RESET . TextFormat::GRAY . "Sneak and click anywhere to confirm purchase of claim."
		]);
		if($sender->getInventory()->canAddItem($item)) {
			$sender->getInventory()->addItem($item);
			$sender->setClaimSession(new ClaimSession($faction->getName(), 'faction', false, $sender));
		} else {
			$sender->sendMessage(TextFormat::RED . "Your inventory is too full!");
		}
	}
}