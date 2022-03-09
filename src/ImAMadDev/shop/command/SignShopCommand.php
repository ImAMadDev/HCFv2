<?php

namespace ImAMadDev\shop\command;

use ImAMadDev\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class SignShopCommand extends Command
{

    public function __construct()
    {
        parent::__construct("signshop", "Manage sign shop", "/signshop", ["ss"]);
        $this->setPermission("sign.shop");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        // TODO: Implement execute() method.
    }
}