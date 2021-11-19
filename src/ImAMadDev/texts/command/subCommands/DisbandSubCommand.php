<?php

namespace ImAMadDev\texts\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\HCF;
use ImAMadDev\manager\TextsManager;

class DisbandSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("disband", "/text disband (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", $args[1]);
		if(TextsManager::getInstance()->existText($name) === false){
			$sender->sendMessage(TextFormat::RED . "Este texto no existe!");
			return;
		}
		TextsManager::getInstance()->removeText($name);
		$sender->sendMessage(TextFormat::GREEN . "Haz eliminado el texto flotante {$name}");
    }
}