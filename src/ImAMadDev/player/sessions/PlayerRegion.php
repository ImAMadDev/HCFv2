<?php

namespace ImAMadDev\player\sessions;

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
        $this->name = $name;
        $this->getPlayer()->sendMessage(TextFormat::RED . "Now entering " . TextFormat::RESET . TextFormat::GRAY . "(" . $name . ")");
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

}