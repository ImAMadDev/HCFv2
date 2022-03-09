<?php

namespace ImAMadDev\shop\command\subCommand;

use ImAMadDev\command\SubCommand;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;

class CreateSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("create", "/signshop create", []);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        // TODO: Implement execute() method.
    }
}