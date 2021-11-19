<?php

namespace ImAMadDev\faction;

use pocketmine\data\bedrock\BiomeIds;
use pocketmine\world\Position;

class FactionRally
{
    /**
     * @var Position
     */
    private Position $position;

    /**
     * @var string
     */
    private string $who;

    /**
     * @param Position $position
     * @param string $who
     */
    public function __construct(Position $position, string $who)
    {
        $this->position = $position;
        $this->who = $who;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @param Position $position
     */
    public function setPosition(Position $position): void
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getWho(): string
    {
        return $this->who;
    }

    public function getPositionString(): string
    {
        return "X: " . $this->getPosition()->getFloorX() .
            " Y: " . $this->getPosition()->getFloorY() .
            " Z: " . $this->getPosition()->getFloorZ() .
            " " . $this->getDimension();
    }

    private function getDimension() : string
    {
        if($this->getPosition()->getWorld()->getBiomeId($this->getPosition()->getFloorX(), $this->getPosition()->getFloorZ()) == BiomeIds::HELL) return "Nether";
        return "Overworld";
    }
}