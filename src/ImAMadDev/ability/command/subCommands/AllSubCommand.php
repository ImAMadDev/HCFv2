<?php

namespace ImAMadDev\ability\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\AbilityManager;
use ImAMadDev\ability\Ability;

class AllSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("all", "/ability all (string: Ability name) (int: Ability count)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1]) or !isset($args[2])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(($ability = AbilityManager::getInstance()->getAbilityByName($args[1])) instanceof Ability) {
            if ($ability->getName() === "RankSharp"){
                $sender->sendMessage(TextFormat::RED . "This item can only be obtained using /rank get (rank) (duration)");
                return;
            }
			foreach($this->getServer()->getOnlinePlayers() as $player) {
				if($player instanceof HCFPlayer) {
					$ability->obtain($player, intval($args[2]));
				}
			}
		} else {
			$sender->sendMessage(TextFormat::RED . "Error this crate key doesn't exist!");
		}
    }
}