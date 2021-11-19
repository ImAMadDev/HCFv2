<?php

namespace ImAMadDev\kit;

use ImAMadDev\HCF;
use ImAMadDev\manager\KitManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\InventoryUtils;

class KitCreatorSession
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

    public function setPermission(string $permission) : void
    {
        $this->data["permission"] = $permission;
    }

    public function setDescription(string $description) : void
    {
        $this->data["description"] = $description;
    }

    public function setCountdown(int $countdown) : void
    {
        $this->data["countdown"] = $countdown;
    }

    public function setSlot(int $slot) : void
    {
        $this->data["slot"] = $slot;
    }

    public function copyInventory() : void
    {
        $this->data["Armor"] = InventoryUtils::encode($this->getPlayer()->getArmorInventory(), "Armor");
        $this->data["Inventory"] = InventoryUtils::encode($this->getPlayer()->getInventory());
    }

    public function setIcon() : void
    {
        $i = $this->getPlayer()->getInventory()->getItemInHand();
        $this->data["icon"] = implode(":", [$i->getId(), $i->getDamage(), 1]);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function setCustomName(string $name) : void
    {
        $this->data["customName"] = $name;
    }

    public function save() : void
    {
        KitManager::getInstance()->createCustomKit($this);
    }
}