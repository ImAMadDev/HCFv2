<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\HCF;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\Tile;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\block\Block;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\command\Command;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class EnderChestCommand extends Command{
	
	public function __construct(){
		parent::__construct("echest", "Open your enderchest", "/echest", ["virtualchest", "ec"]);
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void{
		if($sender instanceof HCFPlayer){

            $this->sendChest($sender);
		} else {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
		}
	}

    public function sendChest(HCFPlayer $player){
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName( $player->getName() . "'s Enderchest");
        $menu->getInventory()->setContents($player->getEnderInventory()->getContents());
        $menu->setListener(function (InvMenuTransaction $transaction) : InvMenuTransactionResult {
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory) : void {
            $player->getEnderInventory()->setContents($inventory->getContents());
        });
        $menu->send($player);
    }

}