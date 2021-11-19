<?php

namespace ImAMadDev\texts\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\TextsManager;

class CreateSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("create", "/text create (string: name) (string: text)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!isset($args[1]) or !isset($args[2])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", $args[1]);
		if(TextsManager::getInstance()->existText($name) === true){
			$sender->sendMessage(TextFormat::RED . "Este texto ya existe!");
			return;
		}
		array_shift($args);
		array_shift($args);
		$message = implode(" ", $args);
		$message = str_replace('\n', '{line}', $message);
		TextsManager::getInstance()->addText($name, $message, $sender->getPosition());
		$sender->sendMessage(TextFormat::GREEN . "Haz creado el texto flotante $name con el mensaje $message en tu posicion");
    }
}