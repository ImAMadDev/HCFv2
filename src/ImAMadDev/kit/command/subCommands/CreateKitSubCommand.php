<?php

namespace ImAMadDev\kit\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\kit\Kit;
use ImAMadDev\manager\KitManager;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateKitSubCommand extends SubCommand
{
    #[Pure] public function __construct() {
        parent::__construct("create", "/kit create (string: kit name)");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission("kit.command")){
            $sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
            return;
        }
        if (!$sender instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
            return;
        }
        if (!isset($args[1])) {
            $sender->sendMessage($this->getUsage());
            return;
        }
        if (KitManager::getInstance()->getKitByName($args[1]) instanceof Kit) {
            $sender->sendMessage(TextFormat::RED . "Error this kit already exist!");
        } else {
            KitManager::getInstance()->openSession($sender, $args[1]);
        }
    }
}