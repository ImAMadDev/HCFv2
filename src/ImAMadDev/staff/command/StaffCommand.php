<?php /** @noinspection ALL */

namespace ImAMadDev\staff\command;

use ImAMadDev\command\Command;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\staff\command\subCommands\ChatSubCommand;
use ImAMadDev\staff\command\subCommands\EnderchestSubCommand;
use ImAMadDev\staff\command\subCommands\InvSubCommand;
use ImAMadDev\youtubers\redeem\command\subCommand\AddSubCommand;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class StaffCommand extends Command
{

    public function __construct()
    {
        parent::__construct('staff', 'ModMode Command', '/mod help', ['mod']);
        $this->addSubCommand(new EnderchestSubCommand());
        $this->addSubCommand(new InvSubCommand());
        $this->addSubCommand(new ChatSubCommand());
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('staff.command')){
            $sender->sendMessage(new Translatable('pocketmine.command.notFound', ['{commandName}' => $commandLabel, '{helpCommand}' => 'help']));
            return;
        }
        if(isset($args[0])) {
            $subCommand = $this->getSubCommand($args[0]);
            if($subCommand !== null) {
                $subCommand->execute($sender, $commandLabel, $args);
                return;
            }
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
    }
}