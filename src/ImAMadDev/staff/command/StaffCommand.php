<?php

namespace ImAMadDev\staff\command;

use ImAMadDev\command\Command;
use ImAMadDev\staff\command\subCommands\ChatSubCommand;
use ImAMadDev\staff\command\subCommands\CopyInvSubCommand;
use ImAMadDev\staff\command\subCommands\PasteInvSubCommand;
use ImAMadDev\staff\command\subCommands\EnderchestSubCommand;
use ImAMadDev\staff\command\subCommands\InvSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

class StaffCommand extends Command
{

    public function __construct()
    {
        parent::__construct('staff', 'ModMode Command', '/mod help', ['mod']);
        $this->addSubCommand(new EnderchestSubCommand());
        $this->addSubCommand(new InvSubCommand());
        $this->addSubCommand(new ChatSubCommand());
        $this->addSubCommand(new CopyInvSubCommand());
        $this->addSubCommand(new PasteInvSubCommand());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('staff.command')){
            $sender->sendMessage(new Translatable('pocketmine.command.notFound', ['commandName' => $commandLabel, 'helpCommand' => 'help']));
            return;
        }
        if(isset($args[0])) {
            $subCommand = $this->getSubCommand($args[0]);
            if($subCommand !== null) {
                $subCommand->execute($sender, $commandLabel, $args);
                return;
            }
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
        }
    }
}