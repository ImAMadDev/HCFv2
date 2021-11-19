<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TellCommand extends Command {
	
	public function __construct() {
		parent::__construct("tell", "Send a message to a player.", "/tell <player> <message>", ["whisper", "w", "message", "msg"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(count($args) < 2) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$name = array_shift($args);
		$player = $this->getServer()->getPlayerByPrefix($name);
		if($sender === $player or $player === null) {
			$sender->sendMessage(TextFormat::RED . "Invalid Player!");
			return;
		}
		$message = implode(" ", $args);
		$sender->sendMessage(TextFormat::GRAY . TextFormat::BOLD . "TO {$player->getName()}: " . TextFormat::RESET . TextFormat::GREEN . $message);
		$player->sendMessage(TextFormat::GRAY . TextFormat::BOLD . "FROM {$sender->getName()}: " . TextFormat::RESET . TextFormat::GREEN . $message);
	}
}