<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\utils\Limits;

class BardClass extends IEnergyClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Bard', [ItemIds::GOLD_HELMET, ItemIds::GOLDEN_CHESTPLATE, ItemIds::GOLDEN_LEGGINGS, ItemIds::GOLDEN_BOOTS]);
    }

    /**
     * @return array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 1, false),
                new EffectInstance(VanillaEffects::REGENERATION(), Limits::INT32_MAX, 1, false),
                new EffectInstance(VanillaEffects::RESISTANCE(), Limits::INT32_MAX, 1, false)
            ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return float|int
     */
    public function getMaxEnergy(): float|int
    {
        return 120.0;
    }
}