<?php

namespace ImAMadDev\texts\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

use ImAMadDev\command\SubCommand;
use ImAMadDev\manager\TextsManager;

class EditSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("edit", "/text edit (string: name) (string: text)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1]) or !isset($args[2])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", $args[1]);
		if(TextsManager::getInstance()->existText($name) === false){
			$sender->sendMessage(TextFormat::RED . "Este texto no existe!");
			return;
		}
		array_shift($args);
		array_shift($args);
		$message = implode(" ", $args);
		$message = str_replace('\n', '{line}', $message);
		TextsManager::getInstance()->editText($name, $message);
		$sender->sendMessage(TextFormat::GREEN . "Haz editado el texto flotante {$name} con el mensaje {$message}");
    }
}