<?php

namespace ImAMadDev\staff\command;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use pocketmine\Server;

class SudoCommand extends Command
{

    public function __construct()
    {
        parent::__construct('sudo', 'ModMode Command', '/sudo (player) (message)');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('staff.command')){
            $sender->sendMessage(new Translatable('pocketmine.command.notFound', ['{commandName}' => $commandLabel, '{helpCommand}' => 'help']));
            return;
        }
        if(count($args) >= 2) {
        	$player = $this->getServer()->getPlayerByPrefix($args[0]);
       	 if($player instanceof HCFPlayer) {
       		$player->chat(implode(" ", array_shift($args)));
       	}
        }
        $sender->sendMessage(TextFormat::RED . $this->getUsage());
    }
}