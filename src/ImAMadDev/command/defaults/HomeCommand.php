<?php

namespace ImAMadDev\command\defaults;

use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\command\Command;
use ImAMadDev\faction\ticks\TeleportHomeTask;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\manager\SOTWManager;
use ImAMadDev\player\HCFPlayer;
use pocketmine\command\CommandSender;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\utils\TextFormat;

class HomeCommand extends Command
{

    public function __construct() {
        parent::__construct("home", "Go to your faction home.", "/home", ["hq"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof HCFPlayer) {
            $sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
            return;
        }
        if($sender->getFaction() === null) {
            $sender->sendMessage(TextFormat::RED . "You must be in a Faction to do this!");
            return;
        }
        if($sender->getFaction()->getHome() === null) {
            $sender->sendMessage(TextFormat::RED . "Your faction home is not set!");
            return;
        }
        $time = 15;
        if($sender->isInvincible() and SOTWManager::isEnabled() === false) {
            $sender->sendMessage(TextFormat::RED . "You may not enter here while your pvp timer is active!");
            return;
        }
        $claim = ClaimManager::getInstance()->getClaimByPosition($sender->getPosition()) == null ? null : ClaimManager::getInstance()->getClaimByPosition($sender->getPosition());
        if($claim?->getClaimType()->getType() == ClaimType::SPAWN) {
            $home = $sender->getFaction()->getHome();
            $sender->teleport($home);
            $sender->sendMessage(TextFormat::GREEN . "You have successfully teleport to your home location.");
            $sender->getWorld()->addSound($sender->getPosition()->asVector3(), new EndermanTeleportSound());
            return;
        }
        if($claim !== null) {
            if(!$claim->getFaction()?->isInFaction($sender->getName())) {
                $time = 20;
            }
            if($claim->getFaction()?->isInFaction($sender->getName())) {
                $time = 10;
            }
        }
        if($sender->getCooldown()->has('combattag')) {
            $sender->sendMessage(TextFormat::RED . "You're combat tagged!");
            return;
        }
        if($sender->getCooldown()->has('home_teleport')) {
            $sender->sendMessage(TextFormat::RED . "Already in home cooldown!");
            return;
        }
        if($sender->getCooldown()->has('stuck_teleport')) {
            $sender->sendMessage(TextFormat::RED . "Already in stuck cooldown!");
            return;
        }
        if($sender->getCooldown()->has('logout')) {
            $sender->sendMessage(TextFormat::RED . "Already in logout cooldown!");
            return;
        }
        $sender->getCooldown()->add('home_teleport', $time);
        $this->getMain()->getScheduler()->scheduleRepeatingTask(new TeleportHomeTask($sender), 20);
    }
}