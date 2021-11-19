<?php

namespace ImAMadDev\events\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\HCF;
use ImAMadDev\manager\EventManager;

class CreateSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("create", "/event create (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if($sender->isOp() === false) {
			$sender->sendMessage(TextFormat::RED . "You don't have permission!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if(HCF::$EventsManager->creatorExists($sender)) {
			$sender->sendMessage(TextFormat::RED . "Ya estas en una sesion");
			return;
		}
		array_shift($args);
		$tag = implode(" ", $args);
		$name = str_replace(" ", "_", TextFormat::clean($tag));
		if(HCF::$EventsManager->eventExists($name)) {
			$sender->sendMessage(TextFormat::RED . "Event {$name} already exist");
			return;
		}
		$sender->sendMessage(TextFormat::GREEN . HCF::$EventsManager->addCreator($sender, $name));
    }
}