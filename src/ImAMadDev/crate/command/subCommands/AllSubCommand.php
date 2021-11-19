<?php

namespace ImAMadDev\crate\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

use ImAMadDev\command\SubCommand;
use ImAMadDev\manager\CrateManager;
use ImAMadDev\crate\Crate;

class AllSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("all", "/crate all (string: Crate name) (int: Crate count)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!isset($args[1]) or !isset($args[2])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(($crate = CrateManager::getInstance()->getCrateByName($args[1])) instanceof Crate) {
			foreach($this->getServer()->getOnlinePlayers() as $player) {
				if($player->getInventory()->canAddItem($crate->getCrateKey(intval($args[2])))) {
					$player->getInventory()->addItem($crate->getCrateKey(intval($args[2])));
				} else {
					$player->getWorld()->dropItem($player->getPosition()->asVector3(), $crate->getCrateKey(intval($args[2])), new Vector3(0, 0, 0));
				}
				$player->sendMessage(TextFormat::YELLOW . "You have received: x $args[2]" . TextFormat::DARK_PURPLE . TextFormat::BOLD . $crate->getName());
			}
		} else {
			$sender->sendMessage(TextFormat::RED . "Error this crate key doesn't exist!");
		}
    }
}