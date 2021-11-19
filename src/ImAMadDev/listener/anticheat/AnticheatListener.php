<?php

namespace ImAMadDev\listener\anticheat;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

class AnticheatListener implements Listener
{


    private static AnticheatListener $instance;
    private static HCF $main;

    public function __construct(HCF $main) {
        self::$main = $main;
        self::$instance = $this;
    }

    public function sendAlertToStaff(HCFPlayer $cheater) : void {
        foreach(self::$main->getServer()->getOnlinePlayers() as $player) {
            if($player->hasPermission('staff.alert') === true) {
                $player->sendMessage(TextFormat::RED . "AntiCheat > " . TextFormat::GRAY . $cheater->getName() . TextFormat::BLUE . " Suspect using AutoClick, CPS: " . TextFormat::RED . $this->getCps($cheater));
            }
        }
    }

}