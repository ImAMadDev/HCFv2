<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\ability\Ability;
use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class GetSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("get", "/rank get (rank) (duration)", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(count($args) < 2) {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        if(isset($args[2])) {
            if(!preg_match("/(^[1-9][0-9]{0,2}[mhd])(,[1-9][0-9]{0,2}[mhd]){0,2}$/", $args[2])) {
                $sender->sendMessage(TextFormat::RED . "Unknown countdown type {$args[3]}");
                return;
            }
        } else {
            $args[2] = "permanent";
        }
        if (($ability = HCF::$abilityManager->getAbilityByName("RankSharp")) instanceof  Ability){
            $item = $ability->get(1, ["rank" => $args[1], "duration" => $args[2]]);
            if ($sender instanceof HCFPlayer) {
                $sender->getInventory()->addItem($item);
            }
        }
    }
}