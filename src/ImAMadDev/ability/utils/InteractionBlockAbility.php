<?php

namespace ImAMadDev\ability\utils;


use ImAMadDev\ability\Ability;
use ImAMadDev\player\HCFPlayer;
use pocketmine\block\Block;
use pocketmine\math\Facing;
use pocketmine\player\Player;

abstract class InteractionBlockAbility extends Ability
{

    abstract public function consume(HCFPlayer|Player $player, Block $block, int $face = Facing::UP) : void;

}