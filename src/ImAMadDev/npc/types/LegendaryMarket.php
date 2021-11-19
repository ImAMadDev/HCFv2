<?php

namespace ImAMadDev\npc\types;

use ImAMadDev\manager\FormManager;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ImAMadDev\npc\NPCEntity;

class LegendaryMarket extends NPCEntity {
	
	public function getName() : string {
		return TextFormat::GOLD . "Legendary Market" . TextFormat::EOL . TextFormat::GRAY . "Attack to open shop";
	}

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if($player->isOnline()) {
            $player->sendMessage("Opening Legendary Market");
        }
        return parent::onInteract($player, $clickPos);
    }

}