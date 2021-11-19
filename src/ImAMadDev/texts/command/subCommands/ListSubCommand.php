<?php

namespace ImAMadDev\texts\command\subCommands;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\HCF;
use ImAMadDev\manager\TextsManager;

class ListSubCommand extends SubCommand {
	
	public function __construct() {
		parent::__construct("list", "/text list");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		$sender->sendMessage(TextFormat::GREEN . TextsManager::getInstance()->getTextList());
		return;
    }

}