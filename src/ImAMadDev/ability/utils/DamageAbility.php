<?php

namespace ImAMadDev\ability\utils;

use ImAMadDev\ability\Ability;
use ImAMadDev\player\HCFPlayer;
use pocketmine\player\Player;

abstract class DamageAbility extends Ability
{

    abstract public function consume(HCFPlayer|Player $player) : void;

}