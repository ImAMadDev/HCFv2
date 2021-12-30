<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\kit\classes\IClass;
use ImAMadDev\kit\classes\IEnergyClass;
use ImAMadDev\player\HCFPlayer;

class ClassEnergy
{

    private IClass|null $storageClass = null;

    public function __construct(
        private HCFPlayer $player,
        private float $energy = 0.0,
    ){}

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    /**
     * @return float
     */
    public function getEnergy(): float
    {
        return $this->energy;
    }

    /**
     * @param float $energy
     */
    public function setEnergy(float $energy): void
    {
        $this->energy = $energy;
    }

    /**
     * @param float $toAdd
     * @return void
     */
    public function add(float $toAdd) : void
    {
        $this->energy += $toAdd;
    }

    public function reduce(float $reduce) : void
    {
        $this->energy -= $reduce;
    }

    public function onTick() : void
    {
        if ($this->getPlayer()->getClass() === null and $this->getEnergy() > 0.0) {
            $this->setEnergy(0.0);
            return;
        }
        if (($class = $this->getPlayer()->getClass()) instanceof IEnergyClass) {
            if ($this->storageClass?->name !== $class->name) {
                $this->setEnergy(0.0);
            }
            if ($this->getEnergy() >= $class->getMaxEnergy()) return;
            $this->add(0.5);
        }
    }

    /**
     * @param IClass|null $storageClass
     */
    public function setStorageClass(?IClass $storageClass): void
    {
        $this->storageClass = $storageClass;
    }

    /**
     * @return IClass|null
     */
    public function getStorageClass(): ?IClass
    {
        return $this->storageClass;
    }

}