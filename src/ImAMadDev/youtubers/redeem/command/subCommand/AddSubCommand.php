<?php

namespace ImAMadDev\youtubers\redeem\command\subCommand;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use ImAMadDev\youtubers\redeem\RedeemCreator;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("add", "/redeem add (content creator)", ["register"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission("redeem.command")){
            $sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
            return;
        }
        if (isset($args[1])){
            if (HCF::getRedeemManager()->getRedeem($args[1]) instanceof RedeemCreator){
                $sender->sendMessage(TextFormat::RED . "This content creator already exists");
            } else {
                HCF::getRedeemManager()->registerRedeem($args[1]);
                $sender->sendMessage(TextFormat::colorize("&aYou have registered a new content creator: &9") . $args[1]);
            }
        } else {
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        }
    }
}