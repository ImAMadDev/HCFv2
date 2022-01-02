<?php

namespace ImAMadDev\npc\types;

use ImAMadDev\HCF;
use ImAMadDev\npc\NPCEntity;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TopThree extends NPCEntity
{
    protected bool $canUpdateTag = false;

    public function getName(): string
    {
        if (isset(HCF::getInstance()->getTopKills()[2])) return TextFormat::GOLD . "Top #3" . TextFormat::EOL .
            TextFormat::AQUA . HCF::getInstance()->getTopKills()[2]["name"] . TextFormat::EOL .
            TextFormat::RED . "Kills: " . HCF::getInstance()->getTopKills()[2]["kills"] . TextFormat::EOL;
        return TextFormat::GOLD . "Top #3" . TextFormat::EOL .
            TextFormat::AQUA . "Loading" . TextFormat::EOL .
            TextFormat::RED . "Kills: Loading" . TextFormat::EOL;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        if ((time() % 300) == 0) {
            if (isset(HCF::getInstance()->getTopKills()[2])) {
                $this->setSkin(HCFUtils::getSkin(HCF::getInstance()->getTopKills()[2]["name"]));
            }
            $this->setNameTag($this->getName());
            $this->setNameTagVisible(true);
            $this->setNameTagAlwaysVisible(true);
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
