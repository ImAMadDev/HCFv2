<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\player\PlayerData;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LFFCommand extends Command
{

    public function __construct()
    {
        parent::__construct("lff", "Locking for faction command", "/lff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof HCFPlayer){
            if ($sender->hasPermission("lff.command")){
                $time = (300 - (time() - PlayerData::getCountdown($sender->getName(), 'lff')));
                if($time > 0) {
                    $sender->sendMessage(TextFormat::RED . "You can't do /lff because you have a countdown of " . gmdate('i:s', $time));
                    return;
                }
                Server::getInstance()->broadcastMessage(TextFormat::colorize('&7&m--------------------------------'. TextFormat::EOL . $sender->getName() . ' &eIs Looking For A Faction' . TextFormat::EOL . '&7&m--------------------------------'));
                PlayerData::setCountdown($sender->getName(), 'lff', (time() + 300));
            } else{
                $sender->sendMessage(TextFormat::RED . "You have not permissions to use this command");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
        }
    }
}