<?php

namespace ImAMadDev\crate\command;

use ImAMadDev\command\Command;
use ImAMadDev\crate\command\subCommands\AllSubCommand;
use ImAMadDev\crate\command\subCommands\CreateSubCommand;
use ImAMadDev\crate\command\subCommands\ListSubCommand;
use ImAMadDev\manager\CrateManager;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\crate\Crate;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

class CrateCommand extends Command {
	
	public function __construct() {
		parent::__construct("crate", "Manage Crates", "/crate help <1-5>", ["key"]);
		$this->addSubCommand(new AllSubCommand());
		$this->addSubCommand(new ListSubCommand());
		$this->addSubCommand(new CreateSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("crate.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			} elseif(($player = $this->getServer()->getPlayerByPrefix($args[0])) instanceof HCFPlayer) {
				if(!isset($args[1]) or !isset($args[2])) {
					$sender->sendMessage("/crate {$player->getName()} (string: crate) (int: number)");
					return;
				}
				if(($crate = CrateManager::getInstance()->getCrateByName($args[1])) instanceof Crate) {
					$keys = $crate->getCrateKey(intval($args[2]));
					if($player->getInventory()->canAddItem($keys)) {
						$player->getInventory()->addItem($keys);
					} else {
						$player->getWorld()->dropItem($player->getPosition()->asVector3(), $keys, new Vector3(0, 0, 0));
					}
					$player->sendMessage(TextFormat::YELLOW . "You have received: x $args[2]" . TextFormat::DARK_PURPLE . TextFormat::BOLD . $crate->getName());
					$sender->sendMessage(TextFormat::GREEN . "You've gave x $args[2] {$crate->getName()} to {$player->getName()}!");
				} else {
					$sender->sendMessage(TextFormat::RED . "This crate doesn't exists!");
				}
				return;
			} elseif(PlayerData::hasData($args[0], "ranks") === true) {
				if(($crate = CrateManager::getInstance()->getCrateByName($args[1])) instanceof Crate) {
					PlayerData::saveItem($args[0], $crate->getName(), $args[2]);
					$sender->sendMessage(TextFormat::GREEN . "You've gave x $args[2] {$crate->getName()} to $args[0]!");
				} else {
					$sender->sendMessage(TextFormat::RED . "This crate doesn't exists!");
				} 
				return;
			}
			$sender->sendMessage("/crate help <1-5>");
		}
	}
}
