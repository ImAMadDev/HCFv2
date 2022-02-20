<?php

namespace ImAMadDev\kit\classes;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\utils\Limits;

class CustomEnergyClass extends IEnergyClass
{
	
	private int | float $energy;
	
	private array $effects = [];
    #[Pure] public function __construct(string $className, array $armor, int | float $energy, array $effects = [])
    {
        parent::__construct($className, $armor);
        $this->energy = $energy;
        $this->effects = $effects;
    }

    /**
     * @return EffectInstance[] array
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

    /**
     * @return float|int
     */
    public function getMaxEnergy(): float|int
    {
        return $this->energy;
    }
}