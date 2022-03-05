<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\utils\{
	TextFormat,
	Limits
};

class MinerClass extends IClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Miner', [ItemIds::IRON_HELMET, ItemIds::IRON_CHESTPLATE, ItemIds::IRON_LEGGINGS, ItemIds::IRON_BOOTS]);
    }

    /**
     * @return array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::NIGHT_VISION(), Limits::INT32_MAX, 0, false),
                new EffectInstance(VanillaEffects::HASTE(), Limits::INT32_MAX, 1, false),
                new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), Limits::INT32_MAX, 0, false)
            ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}