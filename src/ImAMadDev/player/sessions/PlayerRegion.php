<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\claim\Claim;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\player\HCFPlayer;
use pocketmine\utils\TextFormat;

class PlayerRegion
{

    public function __construct(
        private HCFPlayer $player,
        private string $name = 'Unknown'){}

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function set(string $name = 'Wilderness'): void
    {
        $last = $this->name;
        $this->name = $name;
        $this->getPlayer()->getClaimView()->update();
        $this->getPlayer()->sendMessage(TextFormat::RED . "Leaving: " . TextFormat::RESET . TextFormat::GRAY . "(" . $last . "), " . $this->getSafety($last));
        $this->getPlayer()->sendMessage(TextFormat::RED . "Entering: " . TextFormat::RESET . TextFormat::GRAY . "(" . $name . "), " . $this->getSafety($name));
    }

    private function getSafety(string $claim_name) : string
    {
        if (ClaimManager::getInstance()->getClaim($claim_name) instanceof Claim) return ClaimManager::getInstance()->getClaim($claim_name)->getSafety();
        return TextFormat::RED . '(DeathBan)';
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

}