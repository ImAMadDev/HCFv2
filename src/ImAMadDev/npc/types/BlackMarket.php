<?php

namespace ImAMadDev\npc\types;

use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ImAMadDev\npc\NPCEntity;
use ImAMadDev\customenchants\CustomEnchantments;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

class BlackMarket extends NPCEntity {
	
	public function getName() : string {
		return TextFormat::RED . "Black Market" . TextFormat::EOL . TextFormat::GRAY . "Right Click to open shop";
	}

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if($player->isOnline()) {
            $this->getBocks($player);
        }
        return parent::onInteract($player, $clickPos);
    }

    public function getBocks(Player $player) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$menu->setName(TextFormat::GREEN . "Enchants Menu");
		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$item1 = $transaction->getItemClicked();
			$item2 = $transaction->getItemClickedWith();
			if($item1->getId() === ItemIds::ENCHANTED_BOOK && $item2->getId() !== ItemIds::ENCHANTED_BOOK) {
				$player->getInventory()->addItem($item1);
				$player->sendMessage(TextFormat::RED . "An unknown error occurred, try again later!");
			} elseif($item1->getId() !== ItemIds::ENCHANTED_BOOK && $item2->getId() === ItemIds::ENCHANTED_BOOK) {
				$player->getInventory()->addItem($item2);
				$player->sendMessage(TextFormat::RED . "An unknown error occurred, try again later!");
			}
			return $transaction->discard();
		});
		$menu->send($player);
		foreach(CustomEnchantments::getEnchantments() as $enchant) {
			$item = CustomEnchantments::getEnchantedBook($enchant->getName(), rand(1, $enchant->getMaxLevel()));
			$menu->getInventory()->addItem($item);
		}
	}

}