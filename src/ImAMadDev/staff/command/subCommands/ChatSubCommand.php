<?php

namespace ImAMadDev\staff\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\player\PlayerUtils;
use ImAMadDev\player\sessions\ChatMode;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ChatSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct('chat', '/mod chat', ['c']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof HCFPlayer){
            if ($sender->getChatMode()->get() == PlayerUtils::STAFF){
                $sender->getChatMode()->set(PlayerUtils::PUBLIC);
                $sender->sendMessage(TextFormat::GRAY . 'Your chat mode has changed to: ' . TextFormat::LIGHT_PURPLE . 'Public.');
            } else {
                $sender->getChatMode()->set(PlayerUtils::STAFF);
                $sender->sendMessage(TextFormat::GRAY . 'Your chat mode has changed to: ' . TextFormat::LIGHT_PURPLE . 'Staff.');
            }
        }
    }
}