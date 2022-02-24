<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\command\Command;
use ImAMadDev\faction\FactionUtils;
use ImAMadDev\utils\HCFUtils;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListCommand extends Command
{

    public function __construct()
    {
        parent::__construct("list", "see the number of players online", "/list", ["online", "players"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
    	$players = $this->getServer()->getOnlinePlayers();
        $sender->sendMessage(TextFormat::colorize($this->getMessage(count($players))));
    }

    private function getMessage(int $players) : string
    {
        return '&7&m--------------------------------' . TextFormat::EOL .
            '&6&lOnline Players: &r' . $players . TextFormat::EOL .
            '&7&m--------------------------------';
    }
}