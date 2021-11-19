<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\rank\RankClass;
use ImAMadDev\player\{HCFPlayer, PlayerData};
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("remove", "/rank remove (string: player) (string: rank)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(count($args) < 3) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$player = $this->getServer()->getPlayerByPrefix($args[1]);
		if($player === null) {
			if(PlayerData::hasData($args[1], "ranks")) {
				if(!$this->getMain()->getRankManager()->isRank($args[2])) {
					$sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
					return;
				}
				$rank = $this->getMain()->getRankManager()->getRank($args[2]);
				if(!PlayerData::hasRank($args[1], $rank->getName())) {
					$sender->sendMessage(TextFormat::RED . "This player no have this rank!");
					return;
				}
				PlayerData::removeRank($args[1], $rank->getName());
				$sender->sendMessage(TextFormat::GREEN . "You've removed the rank {$args[2]} to the player {$args[1]}!");
            } else {
				$sender->sendMessage(TextFormat::RED . "This player doesn't exist!");
            }
        } else {
			if(!$this->getMain()->getRankManager()->isRank($args[2])) {
				$sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
				return;
			}
			$rank = $this->getMain()->getRankManager()->getRank($args[2]);
			if(!PlayerData::hasRank($player->getName(), $rank->getName())) {
				$sender->sendMessage(TextFormat::RED . "This player no have this rank!");
				return;
			}
			$player->removeRank($rank->getName());
			PlayerData::removeRank($player->getName(), $rank->getName());
			$sender->sendMessage(TextFormat::GREEN . "You've removed the rank {$rank->getName()} to the player {$player->getName()}!");
		}
	}
}