<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\utils\Limits;

class ArcherClass extends IEnergyClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Archer', [ItemIds::LEATHER_CAP, ItemIds::LEATHER_CHESTPLATE, ItemIds::LEATHER_LEGGINGS, ItemIds::LEATHER_BOOTS]);
    }

    /**
     * @return EffectInstance[] array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 2, false),
                new EffectInstance(VanillaEffects::REGENERATION(), Limits::INT32_MAX, 0, false),
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
        return 60.0;
    }
}