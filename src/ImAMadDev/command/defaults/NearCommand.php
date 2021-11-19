<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\math\AxisAlignedBB;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;

class NearCommand extends Command {
	
	public function __construct() {
		parent::__construct("near", "Nearby players", "/near", ["n"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!$sender->hasPermission("near.command")) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		$players = $this->getNearbyPlayers($sender);
		$sender->sendMessage(TextFormat::GRAY . "Nearby players: " . implode(', ', $players));
	}
	
	public function getNearbyPlayers(HCFPlayer $sender) : array {
		$players = [];
        foreach($sender->getWorld()->getNearbyEntities(new AxisAlignedBB($sender->getPosition()->getFloorX() - 200, $sender->getPosition()->getFloorY() - 200, $sender->getPosition()->getFloorZ() - 200, $sender->getPosition()->getFloorX() + 200, $sender->getPosition()->getFloorY() + 200, $sender->getPosition()->getFloorZ() + 200), $sender) as $e){
            if($e->getName() == $sender->getName()) {
                continue;
            }
            $distance = round($sender->getPosition()->distance($e->getPosition()));
            $players[] = TextFormat::colorize("&c" . $e->getName() . " &8[&6{$distance}&8]");
        }
		return $players;
	}
}