<?php

namespace ImAMadDev\crate;

use ImAMadDev\manager\CrateManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\InventoryUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\utils\TextFormat;

class CrateCreateSession
{

    public array $data = [];
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

    public function setKey() : void
    {
        $item = $this->getPlayer()->getInventory()->getItemInHand();
        $this->data["key"] = implode(":", [$item->getId(), $item->getMeta(), 1]);
    }

    public function setDown() : void
    {
        $item = $this->getPlayer()->getInventory()->getItemInHand();
        $this->data["down_block"] = implode(":", [$item->getId(), $item->getMeta(), 1]);
    }

    public function sendChest() : void
    {
        $session = $this;
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName(TextFormat::colorize($this->data["customName"] ?? "Sin Name") . " " . TextFormat::GREEN . "Crate Creation");
        $menu->setInventoryCloseListener(function (HCFPlayer $player) use($session, $menu): void{
            $session->data["Inventory"] = base64_encode(InventoryUtils::encode($menu->getInventory()));
        });
        $menu->send($this->getPlayer());
    }

    public function save() : void
    {
        CrateManager::getInstance()->createCustomCrate($this);
    }
}