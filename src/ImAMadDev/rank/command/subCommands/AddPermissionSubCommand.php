<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\rank\RankClass;
use ImAMadDev\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddPermissionSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("addpermission", "/rank addpermission (string: rank) (string: permission)", ["addperm"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(count($args) < 3) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(!$this->getMain()->getRankManager()->isRank($args[1])) {
			$sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
			return;
		}
		$rank = $this->getMain()->getRankManager()->getRank($args[1]);
		if($rank->hasPermission($args[2])) {
			$sender->sendMessage(TextFormat::RED . "This rank already have this permission");
			return;
		}
		$rank->addPermission($args[2]);
		$sender->sendMessage(TextFormat::GREEN . "You've added the permission {$args[2]} to the rank {$args[1]}!");
	}
}