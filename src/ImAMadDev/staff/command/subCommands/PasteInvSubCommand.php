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
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PasteInvSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct("pinv", "/staff pinv (player_name)", ["pasteinv"]);
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
                $target->getInventory()->setContents($sender->getInventory()->getContents());
                $sender->sendMessage(TextFormat::GREEN . "You have been copied your inventory to inventory: " .$target->getName());
            } else {
                $nbt = Server::getInstance()->getOfflinePlayerData($args[1]);
                if ($nbt instanceof CompoundTag) {
                    $inventoryTag = new ListTag([], NBT::TAG_Compound);
                    $nbt->setTag("Inventory", $inventoryTag);
                    //Normal inventory
                    $slotCount = $sender->getInventory()->getSize() + $sender->getInventory()->getHotbarSize();
                    for($slot = $sender->getInventory()->getHotbarSize(); $slot < $slotCount; ++$slot){
                        $item = $sender->getInventory()->getItem($slot - 9);
                        if(!$item->isNull()){
                            $inventoryTag->push($item->nbtSerialize($slot));
                        }
                    }

                    //Armor
                    for($slot = 100; $slot < 104; ++$slot){
                        $item = $sender->getInventory()->getItem($slot - 100);
                        if(!$item->isNull()){
                            $inventoryTag->push($item->nbtSerialize($slot));
                        }
                    }
                    $sender->sendMessage(TextFormat::GREEN . "You have been copied your inventory to inventory: " . $args[1]);
                } else {
                    $sender->sendMessage(new Translatable('pocketmine.command.error.playerNotFound', [$args[1]]));
                }
            }
        }
    }
}