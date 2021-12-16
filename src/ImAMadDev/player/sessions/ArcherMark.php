<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;

class ArcherMark
{

    public function __construct(
        public HCFPlayer $player,
        private int|float $distance = 0,
    ){}

    /**
     * @return float|int
     */
    public function getDistance(): float|int
    {
        return $this->distance;
    }

    /**
     * @param float|int $distance
     */
    public function setDistance(float|int $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    #[Pure] public function getDamage() : float
    {
        if ($this->distance === 0) return 0;
        if ($this->distance < 5) return 0.1;
        if ($this->getDistance() > 15){
            return 3.8;
        }
        return round(($this->distance / 4), 2);
    }

}