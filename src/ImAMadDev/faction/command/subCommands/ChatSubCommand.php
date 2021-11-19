<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use ImAMadDev\player\{PlayerUtils, HCFPlayer};
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ChatSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("chat", "/faction chat [mode]", ["c"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if($sender->getFaction() === null) {
			$sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
			return;
		}
		if(isset($args[1])) {
            $mode = match ($args[1]) {
                "p", "public" => PlayerUtils::PUBLIC,
                "a", "ally" => PlayerUtils::ALLY,
                default => PlayerUtils::FACTION,
            };
		} else {
			$mode = PlayerUtils::FACTION;
		}
		$sender->setChatMode($mode);
		$sender->sendMessage(TextFormat::GRAY . "Your chat mode has been switched to " . TextFormat::GREEN . strtoupper($this->modeString($sender)));
	}
	
	#[Pure] public function modeString(HCFPlayer $player) : string {
        return match ($player->getChatMode()) {
            PlayerUtils::PUBLIC => "public",
            PlayerUtils::ALLY => "ally",
            PlayerUtils::FACTION => "faction",
            default => "unknown",
        };
	}
}