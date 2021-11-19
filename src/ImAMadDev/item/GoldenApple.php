<?php

namespace ImAMadDev\item;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class GoldenApple extends Food {

	public function requiresHunger() : bool {
		return false;
	}
	
	public function getSaturationRestore() : float {
		return 9.6;
	}
	
	public function getFoodRestore() : int {
		return 6;
	}
	
	public function getAdditionalEffects() : array {
		return [
			new EffectInstance(VanillaEffects::REGENERATION(), 100),
			new EffectInstance(VanillaEffects::ABSORPTION(), 2400),
			new EffectInstance(VanillaEffects::RESISTANCE(), 1400),
		];
	}
}