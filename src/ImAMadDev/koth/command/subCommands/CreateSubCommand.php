<?php

namespace ImAMadDev\koth\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use ImAMadDev\manager\ClaimManager;

class CreateSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("create", "/koth create (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(Server::getInstance()->isOp($sender->getName()) === false) {
			$sender->sendMessage(TextFormat::RED . "You don't have permission!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(HCF::$KOTHManager->creatorExists($sender)) {
			$sender->sendMessage(TextFormat::RED . "You already have session!");
			return;
		}
		$name = str_replace(" ", "_", TextFormat::clean($args[1]));
		if(HCF::$KOTHManager->arenaExists($name)) {
			$sender->sendMessage(TextFormat::RED . "KOTH {$name} already exist");
			return;
		}
		if(ClaimManager::getInstance()->getClaim($name) !== null || $name === "Wilderness"){
			$sender->sendMessage(TextFormat::RED . "This name can't be used");
			return;
		}
		$sender->sendMessage(TextFormat::GREEN . HCF::$KOTHManager->addCreator($sender, $name));
    }
}