<?php

namespace ImAMadDev\staff\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\lang\Translatable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class CopyInvSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("cinv", "/staff cinv (player_name)", ["copyinv"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($sender instanceof HCFPlayer){
            if (empty($args[1])){
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                return;
            }
            $target = Server::getInstance()->getPlayerByPrefix($args[1]);
            if ($target instanceof HCFPlayer) {
                $sender->getInventory()->setContents($target->getInventory()->getContents());
                $sender->sendMessage(TextFormat::GREEN . "You have been copied the inventory of: " .$target->getName());
            } else {
                $namedTag = Server::getInstance()->getOfflinePlayerData($args[1]);
                if ($namedTag instanceof CompoundTag) {
                    $content = $namedTag->getListTag("Inventory");
                    if($content !== null){
                        /** @var CompoundTag $item */
                        foreach($content as $i => $item){
                            $sender->getInventory()->setItem($item->getByte("Slot"), Item::nbtDeserialize($item));
                        }
                    }
                    $sender->sendMessage(TextFormat::GREEN . "You have been copied the inventory of: " . $args[1]);
                } else {
                    $sender->sendMessage(new Translatable('pocketmine.command.error.playerNotFound', [$args[1]]));
                }
            }
        }
    }
}