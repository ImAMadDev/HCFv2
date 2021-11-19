<?php

namespace ImAMadDev\block;

use pocketmine\block\Grass as GrassPMMP;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Grass extends GrassPMMP
{

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        return false;
    }

    public function onRandomTick() : void{}

}