<?php

namespace ImAMadDev\kit\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\kit\classes\IClass;
use ImAMadDev\manager\KitManager;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateClassSubCommand extends SubCommand
{
    #[Pure] public function __construct() {
        parent::__construct("class", "/kit class (string: class name)");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission("class.command")){
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
        if (KitManager::getInstance()->getClassByName($args[1]) instanceof IClass) {
            $sender->sendMessage(TextFormat::RED . "Error this class already exist!");
        } else {
            KitManager::getInstance()->openClassSession($sender, $args[1]);
        }
    }
}