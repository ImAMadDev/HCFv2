<?php

namespace ImAMadDev\npc\types;

use ImAMadDev\HCF;
use ImAMadDev\npc\NPCEntity;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TopTwo extends NPCEntity
{
    protected bool $canUpdateTag = false;

    public function getName() : string {
        if (isset(HCF::getInstance()->getTopKills()[1])) return "Top #2" . TextFormat::EOL .
            HCF::getInstance()->getTopKills()[1]["name"] . TextFormat::EOL .
            "Kills: " . HCF::getInstance()->getTopKills()[1]["kills"] . TextFormat::EOL;
        return "Top #2" . TextFormat::EOL .
            "Loading" . TextFormat::EOL .
            "Kills: Loading" . TextFormat::EOL;
    }

    public function entityBaseTick(int $tickDiff = 1): bool {
        if((time() % 300) == 0){
            if (isset(HCF::getInstance()->getTopKills()[1])){
                $this->setSkin(HCFUtils::getSkin(HCF::getInstance()->getTopKills()[1]["name"]));
            }
            $this->setNameTag($this->getName());
        }
        return parent::entityBaseTick($tickDiff);
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool
    {
        if ($player instanceof HCFPlayer) {
            $player->sendMessage(TextFormat::GRAY . "Your kills: " . $player->getCache()->getInData('kills', true, 0));
        }
        return parent::onInteract($player, $clickPos);
    }
}