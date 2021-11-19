<?php

namespace ImAMadDev\block;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\FlintSteel;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Obsidian extends Opaque {


    public function __construct()
    {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::OBSIDIAN, 0, ItemIds::OBSIDIAN), "Obsidian", new BlockBreakInfo(35.0 /* 50 in PC */, BlockToolType::PICKAXE, ToolTier::DIAMOND()->getHarvestLevel(), 6000.0));
    }

    /**
     * @param Item $item
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     *
     * @return bool
     */
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if($item instanceof FlintSteel) {
            $x_max = $x_min = $this->getPosition()->x;
            for($x = $this->getPosition()->x + 1; $this->getPosition()->getWorld()->getBlockAt($x, $this->getPosition()->y, $this->getPosition()->z)->getId() == BlockLegacyIds::OBSIDIAN; $x++) {
                $x_max++;
            }
            for($x = $this->getPosition()->x - 1; $this->getPosition()->getWorld()->getBlockAt($x, $this->getPosition()->y, $this->getPosition()->z)->getId() == BlockLegacyIds::OBSIDIAN; $x--) {
                $x_min--;
            }
            $count_x = $x_max - $x_min + 1;
            if($count_x >= 4 and $count_x <= 23) {
                $x_max_y = $this->getPosition()->y;
                $x_min_y = $this->getPosition()->y;
                for($y = $this->getPosition()->y; $this->getPosition()->getWorld()->getBlockAt($x_max, $y, $this->getPosition()->z)->getId() == BlockLegacyIds::OBSIDIAN; $y++) {
                    $x_max_y++;
                }
                for($y = $this->getPosition()->y; $this->getPosition()->getWorld()->getBlockAt($x_min, $y, $this->getPosition()->z)->getId() == BlockLegacyIds::OBSIDIAN; $y++) {
                    $x_min_y++;
                }
                $y_max = min($x_max_y, $x_min_y) - 1;
                $count_y = $y_max - $this->getPosition()->y + 2;
                if($count_y >= 5 and $count_y <= 23) {
                    $count_up = 0;
                    for($ux = $x_min; ($this->getPosition()->getWorld()->getBlockAt($ux, $y_max, $this->getPosition()->z)->getId() == BlockLegacyIds::OBSIDIAN and $ux <= $x_max); $ux++) {
                        $count_up++;
                    }
                    if($count_up == $count_x) {
                        for($px = $x_min + 1; $px < $x_max; $px++) {
                            for($py = $this->getPosition()->y + 1; $py < $y_max; $py++) {
                                if($this->getPosition()->getWorld()->getBlockAt($px, $py, $this->getPosition()->z)->getId() === BlockLegacyIds::AIR) {
                                    $this->getPosition()->getWorld()->setBlock(new Vector3($px, $py, $this->getPosition()->z), new Portal());
                                }
                            }
                        }
                        if($player->isSurvival()) {
                            $item = clone $item;
                            $item->applyDamage(1);
                            $player->getInventory()->setItemInHand($item);
                        }
                        return true;
                    }
                }
            }
            $z_max = $z_min = $this->getPosition()->z;
            for($z = $this->getPosition()->z + 1; $this->getPosition()->getWorld()->getBlockAt($this->getPosition()->x, $this->getPosition()->y, $z)->getId() == BlockLegacyIds::OBSIDIAN; $z++) {
                $z_max++;
            }
            for($z = $this->getPosition()->z - 1; $this->getPosition()->getWorld()->getBlockAt($this->getPosition()->x, $this->getPosition()->y, $z)->getId() == BlockLegacyIds::OBSIDIAN; $z--) {
                $z_min--;
            }
            $count_z = $z_max - $z_min + 1;
            if($count_z >= 4 and $count_z <= 23) {
                $z_max_y = $this->getPosition()->y;
                $z_min_y = $this->getPosition()->y;
                for($y = $this->getPosition()->y; $this->getPosition()->getWorld()->getBlockAt($this->getPosition()->x, $y, $z_max)->getId() == BlockLegacyIds::OBSIDIAN; $y++) {
                    $z_max_y++;
                }
                for($y = $this->getPosition()->y; $this->getPosition()->getWorld()->getBlockAt($this->getPosition()->x, $y, $z_min)->getId() == BlockLegacyIds::OBSIDIAN; $y++) {
                    $z_min_y++;
                }
                $y_max = min($z_max_y, $z_min_y) - 1;
                $count_y = $y_max - $this->getPosition()->y + 2;
                if($count_y >= 5 and $count_y <= 23) {
                    $count_up = 0;
                    for($uz = $z_min; ($this->getPosition()->getWorld()->getBlockAt($this->getPosition()->x, $y_max, $uz)->getId() == BlockLegacyIds::OBSIDIAN and $uz <= $z_max); $uz++) {
                        $count_up++;
                    }
                    if($count_up == $count_z) {
                        for($pz = $z_min + 1; $pz < $z_max; $pz++) {
                            for($py = $this->getPosition()->y + 1; $py < $y_max; $py++) {
                                if($this->getPosition()->getWorld()->getBlockAt($this->getPosition()->x, $py, $pz)->getId() === BlockLegacyIds::AIR) {
                                    $this->getPosition()->getWorld()->setBlock(new Vector3($this->getPosition()->x, $py, $pz), new Portal());
                                }
                            }
                        }
                        if($player->isSurvival()) {
                            $item = clone $item;
                            $item->applyDamage(1);
                            $player->getInventory()->setItemInHand($item);
                        }
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onBreak(Item $item, Player $player = null): bool {
        parent::onBreak($item);
        foreach($this->getAllSides() as $i => $block) {
            if($block instanceof Portal) {
                if($block->getSide(Facing::WEST) instanceof Portal or $block->getSide(Facing::EAST) instanceof Portal) {
                    for($x = $block->getPosition()->getFloorX(); $this->getPosition()->getWorld()->getBlockAt($x, $block->getPosition()->getFloorY(), $block->getPosition()->getFloorZ()) == BlockLegacyIds::PORTAL; $x++) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ()) == BlockLegacyIds::PORTAL; $y++) {
                                $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ()) == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                    }
                    for($x = $block->getPosition()->getFloorX() - 1; $this->getPosition()->getWorld()->getBlockAt($x, $block->getPosition()->getFloorY(), $block->getPosition()->getFloorZ()) == BlockLegacyIds::PORTAL; $x--) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ()) == BlockLegacyIds::PORTAL; $y++) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ()) == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                    }
                }
                else {
                    for($z = $block->getPosition()->getFloorZ(); $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY(), $z) == BlockLegacyIds::PORTAL; $z++) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z) == BlockLegacyIds::PORTAL; $y++) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z) == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                    }
                    for($z = $block->getPosition()->getFloorZ() - 1; $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY(), $z) == BlockLegacyIds::PORTAL; $z--) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z) == BlockLegacyIds::PORTAL; $y++) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z) == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                    }
                }
                return true;
            }
        }
        return true;
    }
}