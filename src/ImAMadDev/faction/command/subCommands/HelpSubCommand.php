<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HelpSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("help", "/faction help <1-5>");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		switch($args[1]) {
			case 1:
				$sender->sendMessage(TextFormat::GOLD . TextFormat::BOLD . "Faction Help " . TextFormat::RESET . TextFormat::DARK_GRAY . "(" . TextFormat::GREEN . "1/5" . TextFormat::DARK_GRAY . ")");
				$sender->sendMessage(TextFormat::YELLOW . " /faction ally " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Request to ally with a faction.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction chat " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Switch chatting modes.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction claim " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Claim a chunk of land.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction create " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Create a faction.");
				break;
			case 2:
				$sender->sendMessage(TextFormat::GOLD . TextFormat::BOLD . "Faction Help " . TextFormat::RESET . TextFormat::DARK_GRAY . "(" . TextFormat::GREEN . "2/5" . TextFormat::DARK_GRAY . ")");
				$sender->sendMessage(TextFormat::YELLOW . " /faction demote " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Demote a faction member.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction deposit " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Deposit into your faction.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction disband " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Disband your faction.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction home " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Teleport to your faction home in the wilderness. (10 seconds)");
				break;
			case 3:
				$sender->sendMessage(TextFormat::GOLD . TextFormat::BOLD . "Faction Help " . TextFormat::RESET . TextFormat::DARK_GRAY . "(" . TextFormat::GREEN . "3/5" . TextFormat::DARK_GRAY . ")");
				$sender->sendMessage(TextFormat::YELLOW . " /faction focus " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Join focus mode");
				$sender->sendMessage(TextFormat::YELLOW . " /faction invite " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Invite a player.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction join " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Accept a faction invite.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction kick " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Kick a faction member.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction leader " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Give faction leadership to another faction member.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction leave " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Leave a faction.");
				break;
			case 4:
				$sender->sendMessage(TextFormat::GOLD . TextFormat::BOLD . "Faction Help " . TextFormat::RESET . TextFormat::DARK_GRAY . "(" . TextFormat::GREEN . "4/5" . TextFormat::DARK_GRAY . ")");
				$sender->sendMessage(TextFormat::YELLOW . " /faction list " . TextFormat::GOLD . "- " . TextFormat::GRAY . "List factions in the order of most online members.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction promote " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Promote a faction member.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction sethome " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Set a home.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction stuck " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Teleport to your faction home in another faction's claim. (30 seconds).");
				$sender->sendMessage(TextFormat::YELLOW . " /faction top " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Show top richest factions.");
				break;
			case 5:
				$sender->sendMessage(TextFormat::GOLD . TextFormat::BOLD . "Faction Help " . TextFormat::RESET . TextFormat::DARK_GRAY . "(" . TextFormat::GREEN . "5/5" . TextFormat::DARK_GRAY . ")");
				$sender->sendMessage(TextFormat::YELLOW . " /faction tl " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Send your current location to your Factions.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction unally " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Remove an ally.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction unfocus " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Remove focus mode");
				$sender->sendMessage(TextFormat::YELLOW . " /faction unclaim " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Unclaim a chunk of land.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction who " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Get info of the faction of a player.");
				$sender->sendMessage(TextFormat::YELLOW . " /faction withdraw " . TextFormat::GOLD . "- " . TextFormat::GRAY . "Withdraw from faction balance.");
				break;
			default:
				$sender->sendMessage(TextFormat::RED . $this->getUsage());
				break;
		}
	}
}
