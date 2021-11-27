<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\HCF;
use ImAMadDev\rank\RankClass;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\command\Command;
use ImAMadDev\manager\ReclaimManager;
use ImAMadDev\player\HCFPlayer;

class ReclaimCommand extends Command {
	
	public function __construct() {
		parent::__construct("reclaim", "Reclaim command.", "/reclaim");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$time = (86400 - (time() - $sender->getCache()->getCountdown('reclaim')));
		if($time > 0) {
			$sender->sendMessage(TextFormat::RED . "You can't do /reclaim because you have a countdown of " . gmdate('H:i:s', $time));
			return;
		}
        foreach ($sender->getRanks() as $rank) {
            if ($rank instanceof RankClass){
                if(!empty($rank->getReclaim())){
                    foreach ($rank->getReclaim() as $key) {
                        if($sender->getInventory()->canAddItem($key)) {
                            $sender->getInventory()->addItem($key);
                        } else {
                            $sender->getWorld()->dropItem($sender->getPosition()->asVector3(), $key);
                        }
                        $sender->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::DARK_RED . TextFormat::BOLD . $key->getCustomName());
                    }
                } else {
                    $sender->sendMessage(TextFormat::RED . "It appears there is no reclaim found for this rank {$rank->getName()}!");
                    continue;
                }
                $this->getServer()->broadcastMessage(TextFormat::colorize("&c{$sender->getName()} &7has reclaimed his keys from the &e/reclaim"));
            }
        }
        $sender->getCache()->setCountdown('reclaim', 86400);
	}
}