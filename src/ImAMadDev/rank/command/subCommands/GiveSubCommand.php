<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use ImAMadDev\rank\RankClass;
use ImAMadDev\player\{HCFPlayer, PlayerCache, PlayerData};
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
		if($player instanceof HCFPlayer) {
            if (!$this->getMain()->getRankManager()->isRank($args[2])) {
                $sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
                return;
            }
            $rank = $this->getMain()->getRankManager()->getRank($args[2]);
            if ($player->getCache()->hasDataInArray($rank->getName(), 'ranks')) {
                $sender->sendMessage(TextFormat::RED . "This player already have this rank!");
                return;
            }
            $player->setRank($rank);
            $player->getCache()->addInArray('ranks', $rank->getName());
            if ($time === 'permanent') {
                $player->getCache()->setInData($rank->getName() . COUNTDOWN, 0, true);
                $sender->sendMessage(TextFormat::GREEN . "You've added the rank {$rank->getName()} to the player {$player->getName()}, duration: permanent");
            } else {
                $player->getCache()->setCountdown($rank->getName() . COUNTDOWN, $time);
                $sender->sendMessage(TextFormat::GREEN . "You've added the rank {$rank->getName()} to the player {$player->getName()}, duration: " . HCFUtils::getTimeString($player->getCache()->getCountdown($rank->getName())));
            }
            $player->getCache()->saveData();
        } else {
            $player = HCF::getInstance()->getCache($args[1]);
            if ($player instanceof PlayerCache) {
                if (!$this->getMain()->getRankManager()->isRank($args[2])) {
                    $sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
                    return;
                }
                $rank = $this->getMain()->getRankManager()->getRank($args[2]);
                if ($player->hasDataInArray($rank->getName(), 'ranks')) {
                    $sender->sendMessage(TextFormat::RED . "This player already have this rank!");
                    return;
                }
                $player->addInArray('ranks', $rank->getName());
                if ($time === 'permanent') {
                    $player->setInData($rank->getName() . COUNTDOWN, 0, true);
                    $sender->sendMessage(TextFormat::GREEN . "You've added the rank {$args[2]} to the player {$player->getName()}, duration: Permanent");
                } else {
                    $player->setCountdown($rank->getName(),$time);
                    $sender->sendMessage(TextFormat::GREEN . "You've added the rank {$args[2]} to the player {$player->getName()}, duration: " . HCFUtils::getTimeString($time));
                }
                $player->saveData();
            } else {
                $sender->sendMessage(TextFormat::RED . "This player doesn't exist!");
            }
        }
	}
}