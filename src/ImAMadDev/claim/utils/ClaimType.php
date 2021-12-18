<?php

namespace ImAMadDev\claim\utils;

class ClaimType
{

    const FACTION = 'faction';
    const KOTH = 'koth';
    const SPAWN = 'spawn';
    const WARZONE = 'warzone';
    const ROAD = 'road';

    public function __construct(
        private string $type = "faction"
    ){}

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

}