<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\utils\Limits;

class RogueClass extends IClass
{

    #[Pure] public function __construct()
    {
        parent::__construct('Rogue', [ItemIds::CHAIN_HELMET, ItemIds::CHAIN_CHESTPLATE, ItemIds::CHAIN_LEGGINGS, ItemIds::CHAIN_BOOTS]);
    }

    /**
     * @return array
     */
    public function getEffects() : array
    {
        return [new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, 1, false),
                new EffectInstance(VanillaEffects::JUMP_BOOST(), Limits::INT32_MAX, 1, false),
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
}