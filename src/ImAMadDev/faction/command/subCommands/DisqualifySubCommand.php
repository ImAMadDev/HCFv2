<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;

class DisqualifySubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("disqualify", "/faction disqualify (string: faction)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("faction.manager")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
		} else {
			$faction = $this->getMain()->getFactionManager()->getFaction($args[1]);
			if($faction === null) {
				$sender->sendMessage(TextFormat::RED . "This faction doesn't exist");
				return;
			}
            if ($faction->isDisqualified()) {
                $faction->unDisqualify();
                $sender->sendMessage(TextFormat::GREEN . "You have qualified for the /f top to the {$faction->getName()} faction.");
            } else {
                $faction->disqualify();
                $sender->sendMessage(TextFormat::GREEN . "You have disqualified the faction {$faction->getName()} from the /f top.");
            }
		}
	}

}