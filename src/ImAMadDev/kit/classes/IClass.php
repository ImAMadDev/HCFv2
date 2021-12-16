<?php

namespace ImAMadDev\kit\classes;

use pocketmine\inventory\ArmorInventory;

abstract class IClass
{

    public function __construct(
        public string $name,
        public array $armor
    ){}

    abstract public function getEffects() : array;

    public function isThis(ArmorInventory $armor): bool
    {
        $helmet = $armor->getHelmet()->getId();
        $chestplate = $armor->getChestplate()->getId();
        $leggings = $armor->getLeggings()->getId();
        $boots = $armor->getBoots()->getId();
        return ($helmet == $this->armor[0] and $chestplate == $this->armor[1] and $leggings == $this->armor[2] and $boots == $this->armor[3]);
    }
}