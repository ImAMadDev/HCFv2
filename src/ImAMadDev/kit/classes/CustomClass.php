<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\data\bedrock\EffectIdMap;
use pocketmine\data\bedrock\EffectIds;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\utils\Limits;

class CustomClass extends IClass
{
	
	private array $effects;

    #[Pure] public function __construct(string $className, array $armor, array $effects =[])
    {
        parent::__construct($className, $armor);
        $this->effects = $effects;
    }

    /**
     * @return array
     */
    public function getEffects() : array
    {
        return $this->effects;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}