<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
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
	
	public function getNearbyPlayers(HCFPlayer $sender): array
    {
		$players = [];
        foreach ($sender->getWorld()->getPlayers() as $player) {
            if ($player instanceof HCFPlayer) {
                if ($player->getXZDistance($sender->getPosition())){
                    if($player->getName() == $sender->getName()) {
                        continue;
                    }
                    $distance = round($sender->getPosition()->distance($player->getPosition()));
                    $players[] = TextFormat::colorize("&c" . $player->getName() . " &8[&6{$distance}&8]");
                }
            }
        }
		return $players;
	}
}