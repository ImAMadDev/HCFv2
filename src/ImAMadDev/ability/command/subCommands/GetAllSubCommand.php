<?php

namespace ImAMadDev\ability\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\AbilityManager;
use ImAMadDev\ability\Ability;

class GetAllSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("getall", "/ability getall");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if($sender instanceof HCFPlayer) {
            foreach (AbilityManager::getInstance()->getAbilities() as $ability) {
                if ($ability instanceof Ability) {
                    if ($ability->getName() == "RankSharp") {
                        continue;
                    }
                    $count = $ability->get()->getMaxStackSize();
                    $ability->obtain($sender, $count);
                }
            }
        }
    }
}