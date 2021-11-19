<?php

namespace ImAMadDev\item;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class EnderEye extends Item {
	
    public function __construct(int $meta = 0){
        parent::__construct(new ItemIdentifier(ItemIds::ENDER_EYE, 0), "Ender Eye");
    }
    public function getMaxStackSize() : int {
        return 64;
    }
}