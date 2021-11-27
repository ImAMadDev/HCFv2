<?php /** @noinspection ALL */

namespace ImAMadDev\staff\command\subCommands;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class InvSubCommand extends SubCommand
{

    #[Pure] public function __construct()
    {
        parent::__construct('inv', '/mod inv (player name)', ['invsee']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender->hasPermission('staff.inventory.command')){
            $sender->sendMessage(new Translatable('pocketmine.command.notFound', ['{commandName}' => $commandLabel, '{helpCommand}' => 'help']));
            return;
        }
        if (!isset($args[1])){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return;
        }
        if ($sender instanceof HCFPlayer) {
            $this->send($sender, $args[1]);
        } else {
            $sender->sendMessage(new Translatable('pocketmine.command.notFound', ['{commandName}' => $commandLabel, '{helpCommand}' => 'help']));
        }
    }

    public function send(Player $sender, string $name){
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName( $name . "'s Inventory");
        $target = Server::getInstance()->getPlayerByPrefix($name);
        if ($target instanceof HCFPlayer) {
            $content = $target->getInventory()->getContents();
            foreach($content as $i => $item){
                $menu->getInventory()->setItem($i, $item);
            }
            for ($i = ($sender->getInventory()->getSize() - 1); $i < $menu->getInventory()->getSize(); $i++){
                $menu->getInventory()->setItem($i, VanillaBlocks::BEDROCK()->asItem()->setCustomName(''));
            }
        } else {
            $namedTag = Server::getInstance()->getOfflinePlayerData($name);
            if ($namedTag instanceof CompoundTag) {
                $content = $namedTag->getListTag("Inventory");
                if($content !== null){
                    /** @var CompoundTag $item */
                    foreach($content as $i => $item){
                        $menu->getInventory()->setItem($item->getByte("Slot"), Item::nbtDeserialize($item));
                    }
                }
                foreach ($menu->getInventory()->getContents() as $slot => $content) {
                    if ($content->getId() == ItemIds::AIR){
                        $menu->getInventory()->setItem($slot, VanillaBlocks::BEDROCK()->asItem()->setCustomName(''));
                    }
                }
            } else {
                $sender->sendMessage(new Translatable('pocketmine.command.error.playerNotFound', [$name]));
                return;
            }
        }
        $menu->setListener(function (InvMenuTransaction $transaction) use ($sender) : InvMenuTransactionResult {
            $inv = $menu->getInventory();
            if($sender->hasPermission('staff.inventory.edit')) {
                if ($transaction->getIn()->getId() === ItemIds::BEDROCK or $transaction->getIn()->getOut() === ItemIds::BEDROCK) {
                    return $transaction->discard();
                }
                return $transaction->continue();
            } else {
                return $transaction->discard();
            }
        });
        $menu->setInventoryCloseListener(function(Player $sender, Inventory $inventory) use($name): void {
            if($sender->hasPermission('staff.inventory.edit')) {
                $target = Server::getInstance()->getPlayerByPrefix($name);
                if ($target instanceof HCFPlayer) {
                    $target->getInventory()->setContents($inventory->getContents());
                } else {
                    $namedTag = Server::getInstance()->getOfflinePlayerData($name);
                    if ($namedTag instanceof CompoundTag) {
                        /** @var CompoundTag[] $items */
                        $items = [];
                        $slotCount = $sender->getInventory()->getSize();
                        for ($slot = 0; $slot < $slotCount; ++$slot) {
                            $item = $inv->getItem($slot);
                            if (!$item->isNull() and $item->getId() !== ItemIds::BEDROCK) {
                                $items[] = $item->nbtSerialize($slot);
                            }
                            $namedTag->setTag("Inventory", new ListTag($items, NBT::TAG_Compound));
                        }
                        Server::getInstance()->saveOfflinePlayerData($name, $namedTag);
                    }
                }
            }
        });
        $menu->send($sender);
    }
}