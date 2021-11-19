<?php

namespace ImAMadDev\item;

use pocketmine\entity\effect\{EffectInstance, VanillaEffects};
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class GoldenAppleEnchanted extends GoldenApple {
	
	public function getAdditionalEffects() : array {
		return [
			new EffectInstance(VanillaEffects::REGENERATION(), 600, 4),
			new EffectInstance(VanillaEffects::ABSORPTION(), 2400, 3),
			new EffectInstance(VanillaEffects::RESISTANCE(), 6000),
			new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 6000)
		];
	}
}