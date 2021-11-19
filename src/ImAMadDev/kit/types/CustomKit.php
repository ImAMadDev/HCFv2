<?php

namespace ImAMadDev\kit\types;

use ImAMadDev\kit\Kit;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class CustomKit extends Kit
{
    private string $name;

    private string $permission;

    private array $armor;

    private array $items;

    private Item $icon;

    private string $description;

    private int $slot;

    private int $countdown;

    public function __construct(string $name, string $permission, array $armor, array $items, Item $icon, string $description, int $countdown, string $customName, int $slot = 0)
    {
        $this->name = $name;
        $this->permission = $permission;
        $this->armor = $armor;
        $this->items = $items;
        $this->icon = $icon->setCustomName(TextFormat::colorize($customName));
        $this->description = $description;
        $this->slot = $slot;
        $this->countdown = $countdown;
    }

    /**
     * @return int
     */
    public function getSlot(): int
    {
        return $this->slot;
    }

    /**
     * @return int
     */
    public function getCooldown(): int
    {
        return $this->countdown;
    }

    /**
     * @return string
     */
    public function getPermission(): string
    {
        return $this->permission;
    }

    /**
     * @return array
     */
    public function getArmor(): array
    {
        return $this->armor;
    }

    /**
     * @return Item
     */
    public function getIcon(): Item
    {
        return  $this->icon;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return bool
     */
    #[Pure] public function isKit(string $name): bool
    {
        return strtolower($this->getName()) === strtolower($name);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function giveKit(HCFPlayer $player) : void
    {
        foreach($this->getItems() as $item) {
            if($player->getInventory()->canAddItem($item)) {
                $player->getInventory()->addItem($item);
            } else {
                $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
            }
        }
        foreach($this->getArmor() as $item) {
            if($player->getArmorInventory()->canAddItem($item)) {
                $player->getArmorInventory()->addItem($item);
            } else {
                $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
            }
        }
        $player->sendMessage(TextFormat::YELLOW . "You have received the Kit: " . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . $this->getName());
    }
}