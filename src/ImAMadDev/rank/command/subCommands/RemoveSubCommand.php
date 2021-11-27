<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use ImAMadDev\player\{HCFPlayer, PlayerCache};
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
		if($player instanceof HCFPlayer) {
            if (!$this->getMain()->getRankManager()->isRank($args[2])) {
                $sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
                return;
            }
            $rank = $this->getMain()->getRankManager()->getRank($args[2]);
            if (!$player->getCache()->hasDataInArray($rank->getName())) {
                $sender->sendMessage(TextFormat::RED . "This player no have this rank!");
                return;
            }
            $player->removeRank($rank->getName());
            $player->getCache()->removeInArray('ranks', $rank->getName());
            $sender->sendMessage(TextFormat::GREEN . "You've removed the rank {$rank->getName()} to the player {$player->getName()}!");
        } else {
            $player = HCF::getInstance()->getCache($args[1]);
            if ($player instanceof PlayerCache) {
                if (!$this->getMain()->getRankManager()->isRank($args[2])) {
                    $sender->sendMessage(TextFormat::RED . "This rank doesn't exist!");
                    return;
                }
                $rank = $this->getMain()->getRankManager()->getRank($args[2]);
                if (!$player->hasDataInArray($rank->getName())) {
                    $sender->sendMessage(TextFormat::RED . "This player no have this rank!");
                    return;
                }
                $player->removeInArray('ranks', $rank->getName());
                $sender->sendMessage(TextFormat::GREEN . "You've removed the rank {$args[2]} to the player {$player->getName()}!");
            } else {
                $sender->sendMessage(TextFormat::RED . "This player doesn't exist!");
            }
        }
	}
}