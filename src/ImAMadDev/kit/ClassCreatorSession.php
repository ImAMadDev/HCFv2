<?php

namespace ImAMadDev\kit;

use ImAMadDev\HCF;
use ImAMadDev\manager\KitManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\InventoryUtils;

class ClassCreatorSession
{

    private array $data = [];

    private HCFPlayer $player;

    public function __construct(HCFPlayer $player, string $name)
    {
        $this->player = $player;
        $this->data["name"] = $name;
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    public function setName(string $name) : void
    {
        $this->data["name"] = $name;
    }

    public function setEnergy(string $energy) : void
    {
        $this->data["Energy"] = $energy;
    }

    public function addEffect(string $name, int $amplifier, bool $visible): void
    {
        $this->data["Effects"][] = ['name' => $name, 'amplifier' => $amplifier, 'visible' => $visible];
    }

    public function copyInventory() : void
    {
        $this->data["Armor"] = InventoryUtils::encode($this->getPlayer()->getArmorInventory(), "Armor");
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    public function save() : void
    {
        KitManager::getInstance()->createCustomClass($this);
    }
}