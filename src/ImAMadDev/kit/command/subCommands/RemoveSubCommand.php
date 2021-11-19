<?php

namespace ImAMadDev\kit\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\kit\Kit;
use ImAMadDev\manager\KitManager;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveSubCommand extends SubCommand
{

    #[Pure] public function __construct() {
        parent::__construct("remove", "/kit remove (string: kit name)");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission("kit.command")){
            $sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
            return;
        }
        if (!isset($args[1])) {
            $sender->sendMessage($this->getUsage());
            return;
        }
        if (KitManager::getInstance()->getKitByName($args[1]) instanceof Kit) {
            KitManager::getInstance()->removeCustomKit($args[1]);
            $sender->sendMessage(TextFormat::GREEN . "You have remove the kit: $args[1]");
        } else {
            $sender->sendMessage(TextFormat::RED . "Error: this kit not exist");
        }
    }
}