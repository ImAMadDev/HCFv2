<?php

namespace ImAMadDev\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\graphic\InvMenuGraphic;
use muqsit\invmenu\type\InvMenuType;
use muqsit\invmenu\type\util\InvMenuTypeBuilders;
use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

class EnderChestInvMenuType implements InvMenuType
{

    private InvMenuType $inner;

    private Player $player;

    public function __construct(){
        $this->inner = InvMenuTypeBuilders::BLOCK_FIXED()
            ->setBlock(VanillaBlocks::ENDER_CHEST())
            ->setSize(27)
            ->build();
    }

    public function createGraphic(InvMenu $menu, Player $player) : ?InvMenuGraphic{
        $this->player = $player;
        return $this->inner->createGraphic($menu, $player);
    }

    public function createInventory() : Inventory{
        return new EnderChestInventory(Position::fromObject(Vector3::zero(), null), $this->player->getEnderInventory());
    }
}