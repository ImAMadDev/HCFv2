<?php

namespace ImAMadDev\listener\anticheat;

use ImAMadDev\player\HCFPlayer;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class BuggyListener implements Listener
{

    public function onDebugSuffocation(EntityDamageEvent $event) : void
    {
        $player = $event->getEntity();
        if ($event->isCancelled()) return;
        if($player instanceof HCFPlayer and $event->getCause() == EntityDamageEvent::CAUSE_SUFFOCATION){
            $this->sendAlertToStaff($player);
        }
    }

    public function sendAlertToStaff(HCFPlayer $cheater) : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            if($player->hasPermission('staff.alert') === true) {
                $player->sendMessage(TextFormat::RED . "AntiCheat > " . TextFormat::GRAY . $cheater->getName() . TextFormat::BLUE . "is receiving suffocation damage, Claim: " . TextFormat::RED . $cheater->getRegion());
            }
        }
    }

}