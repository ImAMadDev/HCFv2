<?php

namespace ImAMadDev\ability\command;

use ImAMadDev\command\Command;
use ImAMadDev\ability\command\subCommands\AllSubCommand;
use ImAMadDev\ability\command\subCommands\ListSubCommand;
use ImAMadDev\manager\AbilityManager;
use ImAMadDev\player\{PlayerData, HCFPlayer};
use ImAMadDev\ability\Ability;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AbilityCommand extends Command {
	
	public function __construct() {
		parent::__construct("ability", "Manage Abilities", "/ability help <1-5>");
		$this->addSubCommand(new AllSubCommand());
		$this->addSubCommand(new ListSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission("ability.command")) {
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
					$sender->sendMessage("/ability {$player->getName()} (string: ability) (int: number)");
					return;
				}
				if(($ability = AbilityManager::getInstance()->getAbilityByName($args[1])) instanceof Ability) {
                    if ($ability->getName() === "RankSharp"){
                        $sender->sendMessage(TextFormat::RED . "This item can only be obtained using /rank get (rank) (duration)");
                        return;
                    }
					$ability->obtain($player, intval($args[2]));
					$sender->sendMessage(TextFormat::GREEN . "You've gave x $args[2] {$ability->getName()} to {$player->getName()}!");
				} else {
					$sender->sendMessage(TextFormat::RED . "This ability doesn't exists!");
				}
				return;
			} elseif(PlayerData::hasData($args[0], "ranks") === true) {
				if(($ability = AbilityManager::getInstance()->getAbilityByName($args[1])) instanceof Ability) {
					PlayerData::saveItem($args[0], $ability->getName(), $args[2]);
					$sender->sendMessage(TextFormat::GREEN . "You've gave x $args[2] {$ability->getName()} to $args[0]!");
				} else {
					$sender->sendMessage(TextFormat::RED . "This ability doesn't exists!");
				} 
				return;
			}
			$sender->sendMessage("/ability help <1-5>");
		}
	}
	
}