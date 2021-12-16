<?php

namespace ImAMadDev\claim\command\subCommands;

use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\player\sessions\ClaimSession;
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
		parent::__construct("create", "/claim create (string: name) (string: type)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
        if(!isset($args[2])) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
		$name = str_replace(" ", "_", $args[1]);
        if (!in_array(strtolower($args[2]), [ClaimType::SPAWN, ClaimType::KOTH, ClaimType::WARZONE])){
            $sender->sendMessage(TextFormat::RED . "This claim type is invalid {$args[2]}: " . join(',', [ClaimType::SPAWN, ClaimType::KOTH, ClaimType::WARZONE]));
            return;
        }
        $claim_type = strtolower($args[2]);
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
			$sender->setClaimSession(new ClaimSession($name, $claim_type,true, $sender));
			$sender->sendMessage(TextFormat::GREEN . "Now claiming {$name} land!");
		} else {
			$sender->sendMessage(TextFormat::RED . "Your inventory is too full!");
		}
    }
}