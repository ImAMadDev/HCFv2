<?php

namespace ImAMadDev\faction\command\subCommands;

use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LeaveSubCommand extends SubCommand {
	
	
	#[Pure] public function __construct() {
		parent::__construct("leave", "/faction leave");
	}
	
	
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
            return;
        }
        if($sender->getFaction() === null) {
            $sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
            return;
        }
        if($sender->getFaction()->isLeader($sender->getName())) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$sender->getFaction()->message(TextFormat::GREEN . $sender->getName() . TextFormat::RED . " has left the faction!");
		$sender->getFaction()->removeMember($sender->getName());
		$sender->setFaction(null);
        $sender->getCache()->setInData('faction', null);
    }
}