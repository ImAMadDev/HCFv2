<?php

namespace ImAMadDev\block;

use pocketmine\block\Dirt as DirtPMMP;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Dirt extends DirtPMMP
{

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($face === Facing::UP and $item instanceof Hoe){
            $item->applyDamage(1);
            return true;
        }

        return false;
    }

}