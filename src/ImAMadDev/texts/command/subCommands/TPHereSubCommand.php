<?php

namespace ImAMadDev\texts\command\subCommands;

use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\manager\TextsManager;

class TPHereSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("tphere", "/text move (string: name) ()");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
        if ($sender instanceof HCFPlayer) {
            $name = str_replace(" ", "_", $args[1]);
            if (TextsManager::getInstance()->existText($name) === false) {
                $sender->sendMessage(TextFormat::RED . "This text doesnt exist!");
                return;
            }
            TextsManager::getInstance()->moveText($name, $sender->getPosition());
            $sender->sendMessage(TextFormat::GREEN . "You have been teleported {$name} to your location!");
        }
    }
}