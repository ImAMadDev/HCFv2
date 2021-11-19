<?php

namespace ImAMadDev\crate\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\crate\Crate;
use ImAMadDev\manager\CrateManager;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("create", "/crate create (string: crate name)", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
            return;
        }
        if (!isset($args[1])){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        if (CrateManager::getInstance()->getCrateByName($args[1]) instanceof Crate){
            $sender->sendMessage(TextFormat::RED . "Error: this crate $args[1] already exist");
            return;
        }
        CrateManager::getInstance()->openSession($sender, $args[1]);
    }
}