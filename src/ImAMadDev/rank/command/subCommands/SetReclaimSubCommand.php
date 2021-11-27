<?php

namespace ImAMadDev\rank\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\rank\RankClass;
use ImAMadDev\utils\InventoryUtils;
use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

class SetReclaimSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct('setreclaim', '/rank setreclaim (rank)', ['reclaim']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('rank.setreclaim.command')){
            $sender->sendMessage(new Translatable('pocketmine.command.notFound', ['{commandName}' => $commandLabel, '{helpCommand}' => 'help']));
            return;
        }
        if ($sender instanceof HCFPlayer) {
            if (!isset($args[1])) {
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                return;
            }
            $rank = HCF::getRankManager()->getRank($args[1]);
            if ($rank instanceof RankClass) {
                $reclaim = InventoryUtils::encodeItems($sender->getInventory()->getContents(false));
                $rank->setReclaim($reclaim);
                $sender->sendMessage(TextFormat::DARK_AQUA . 'You have configured rank reclaim');
            }
        }
    }
}