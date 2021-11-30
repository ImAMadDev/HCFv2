<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\manager\SOTWManager;

class SOTWCommand extends Command {
	
	public function __construct() {
		parent::__construct("sotw", "Start of the world manager.", "/sotw [on - off]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission('sotw.manager')) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($args[0] == "on"){
			if(SOTWManager::isEnabled()) {
				$sender->sendMessage(TextFormat::RED . "Start of the world is already enabled!");
				return;
			}
			SOTWManager::set(true, 120);
			$sender->sendMessage(TextFormat::GREEN . "You have enabled Start of the world!");
		}
		if($args[0] == "off"){
			if(!SOTWManager::isEnabled()) {
				$sender->sendMessage(TextFormat::RED . "Start of the world is already disabled!");
				return;
			}
			SOTWManager::set(false, 120);
			$sender->sendMessage(TextFormat::GREEN . "You have disabled Start of the world!");
		}
        if ($args[0] == "time"){
            if(!SOTWManager::isEnabled()) {
                $sender->sendMessage(TextFormat::RED . "Start of the world is disabled!");
                return;
            }
            if (!isset($args[1])){
                if(!is_numeric($args[1])) {
                    $sender->sendMessage(TextFormat::RED . "Usage numeric arguments in minutes.");
                    return;
                }
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                return;
            }
            SOTWManager::set(false, $args[1]);
            $sender->sendMessage(TextFormat::GREEN . "You have disabled Start of the world!");
        }
	}
}