<?php

namespace ImAMadDev\claim\utils;

use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\block\Door;
use pocketmine\block\FenceGate;
use pocketmine\block\tile\Container;
use pocketmine\block\Trapdoor;

class EditClaimFlag
{

    public function __construct(
        private array $blocks = [],
        private bool $cancelMovement = false
    ){}

    /**
     * @return array
     */
    public function getBlocks(): array
    {
        return $this->blocks;
    }

    /**
     * @param array $blocks
     */
    public function setBlocks(array $blocks): void
    {
        $this->blocks = $blocks;
    }

    public function run(Block $block) : bool
    {
        if (empty($this->getBlocks())) return false;
        foreach ($this->getBlocks() as $block_) {
            if($this->isCancelMovement() == false) {
                if ($block->getPosition()->getWorld()->getTile($block->getPosition()) instanceof Container) return false;
            }
            if ($block->getIdInfo()->getBlockId() == $block_ or $block instanceof Trapdoor or $block instanceof Door or $block instanceof FenceGate) return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isCancelMovement(): bool
    {
        return $this->cancelMovement;
    }

    /**
     * @param bool $cancelMovement
     */
    public function setCancelMovement(bool $cancelMovement): void
    {
        $this->cancelMovement = $cancelMovement;
    }
}