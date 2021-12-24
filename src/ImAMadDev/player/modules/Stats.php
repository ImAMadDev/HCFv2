<?php

namespace ImAMadDev\player\modules;

use ImAMadDev\player\HCFPlayer;

class Stats
{

    private array $stats = ['kills' => 0, 'deaths' => 0, 'ratio' => 0.0];
    public function __construct(
        private HCFPlayer $player
    ){}

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    public function getKills() : int
    {
        return $this->stats['kills'];
    }
}