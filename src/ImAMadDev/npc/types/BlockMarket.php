<?php

namespace ImAMadDev\npc\types;

use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ImAMadDev\npc\NPCEntity;
use ImAMadDev\manager\FormManager;

class BlockMarket extends NPCEntity {
	
	public function getName() : string {
		return TextFormat::YELLOW . "Block Market" . TextFormat::EOL . TextFormat::GRAY . "Attack to open shop";
	}

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if($player->isOnline()) {
            FormManager::getBlockShopMenu($player);
        }
        return parent::onInteract($player, $clickPos);
    }

}