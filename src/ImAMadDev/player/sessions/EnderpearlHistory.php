<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\player\HCFPlayer;
use pocketmine\entity\Location;
use JetBrains\PhpStorm\Pure;

class EnderpearlHistory
{

    public function __construct(
        public HCFPlayer $player,
        private ?Location $usageLocation = null,
        private int $usageTime = 0
    ){}

    /**
     * @return Location | null
     */
    public function getUsageLocation(): ?Location
    {
        return $this->usageLocation;
    }

    /**
     * @param Location | null $location
     */
    public function setUsageLocation(?Location $location): void
    {
        $this->usageLocation = $location;
        if($location instanceof Location) {
        	$this->usageTime = time();
        }
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    public function has() : bool
    {
        if ($this->usageTime === 0) return false;
        if ($this->getUsageLocation() === null) return false;
        if((time() - $this->usageTime) >= 0){
        	return true;
        } else {
        	return false;
        }
    }
}