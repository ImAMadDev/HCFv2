<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Rally extends Command
{

    public function __construct()
    {
        parent::__construct("rally","put yor location on the scoreboard", "/rally [off]", ["r"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$sender instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
            return;
        }
        if($sender->getFaction() === null) {
            $sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
            return;
        }
        if(isset($args[0])){
            if($args[0] == "off"){
                $sender->getFaction()->openRally(null);
                return;
            }
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        } else {
            $sender->getFaction()->openRally($sender);
            $sender->getFaction()->message(TextFormat::RED . $sender->getName() . TextFormat::GRAY . " was updated the faction rally.");
        }
    }
}