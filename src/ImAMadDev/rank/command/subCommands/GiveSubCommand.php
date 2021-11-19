<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\rank\RankClass;
use ImAMadDev\player\{HCFPlayer, PlayerData};
use ImAMadDev\utils\HCFUtils;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class GiveSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("give", "/rank give (string: player) (string: rank)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(count($args) < 3) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		$time = "permanent";
		if(isset($args[3])) {
            if(!preg_match("/(^[1-9][0-9]{0,2}[mhd])(,[1-9][0-9]{0,2}[mhd]){0,2}$/", $args[3])) {
                $sender->sendMessage(TextFormat::RED . "Unknown countdown type {$args[3]}");
                return;
            }
			$time = HCFUtils::strToSeconds($args[3]);
		}
		$player = $this->getServer()->getPlayerByPrefix($args[1]);
		if($player === null) {
			if(PlayerData::hasData($args[1], "ranks")) {
				if(!$this->getMain()->getRankManager()->isRank($args[2])) {
					$sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
					return;
				}
				$rank = $this->getMain()->getRankManager()->getRank($args[2]);
				if(PlayerData::hasRank($args[1], $rank->getName())) {
					$sender->sendMessage(TextFormat::RED . "This player already have this rank!");
					return;
				}
				PlayerData::addRank($args[1], $rank->getName());
				if($time === 'permanent') {
					PlayerData::setCountdown($args[1], $rank->getName(), 0);
					$sender->sendMessage(TextFormat::GREEN . "You've added the rank {$args[2]} to the player {$args[1]}, duration: Permanent");
				} else {
					PlayerData::setCountdown($args[1], $rank->getName(), (time() + $time));
					$sender->sendMessage(TextFormat::GREEN . "You've added the rank {$args[2]} to the player {$args[1]}, duration: " . HCFUtils::getTimeString($time));
				}
			} else {
				$sender->sendMessage(TextFormat::RED . "This player doesn't exist!");
			}
		} else {
			if(!$this->getMain()->getRankManager()->isRank($args[2])) {
				$sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
				return;
			}
			$rank = $this->getMain()->getRankManager()->getRank($args[2]);
			if(PlayerData::hasRank($player->getName(), $rank->getName())) {
				$sender->sendMessage(TextFormat::RED . "This player already have this rank!");
				return;
			}
			$player->setRank($rank);
			PlayerData::addRank($player->getName(), $rank->getName());
			if($time === 'permanent') {
				PlayerData::setCountdown($player->getName(), $rank->getName(), 0);
				$sender->sendMessage(TextFormat::GREEN . "You've added the rank {$rank->getName()} to the player {$player->getName()}, duration: permanent");
			} else {
				PlayerData::setCountdown($player->getName(), $rank->getName(), (time() + $time));
				$sender->sendMessage(TextFormat::GREEN . "You've added the rank {$rank->getName()} to the player {$player->getName()}, duration: " . HCFUtils::getTimeString(PlayerData::getCountdown($player->getName(), $rank->getName())));
			}
		}
	}
}