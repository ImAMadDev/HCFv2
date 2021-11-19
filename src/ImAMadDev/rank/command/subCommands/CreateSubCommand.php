<?php

namespace ImAMadDev\rank\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class CreateSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("create", "/rank create (string: name) (string: tag fornat) (string: chat format)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(count($args) < 4) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		$name = str_replace(" ", "_", $args[1]);
		$tag = $args[2];
		$format = $args[3];
		if(strlen($name) > 10) {
			$sender->sendMessage(TextFormat::RED . "Rank name only permits 10 characters!");
			return;
		}
		if(HCF::$rankManager->isRank($name)) {
			$sender->sendMessage(TextFormat::RED . "This rank already exist!");
			return;
		}
		HCF::$rankManager->createRank(["name" => $name, "format" => $format, "tag" => $tag, "permissions" => ["default.permission"]]);
		$sender->sendMessage(TextFormat::colorize("You have created the rank {$name} with format {$format}, with nametag {$tag}!"));
    }
}