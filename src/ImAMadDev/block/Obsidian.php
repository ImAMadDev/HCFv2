<?php

namespace ImAMadDev\block;

use ImAMadDev\event\PlayerCreateNetherPortalEvent;
use ImAMadDev\utils\HCFUtils;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
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
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\World;
use SplQueue;

class Obsidian extends Opaque {


    private float $lengthSquared;

    public function __construct()
    {
        $this->lengthSquared = (new Vector2(30, 30))->lengthSquared();
        parent::__construct(new BlockIdentifier(BlockLegacyIds::OBSIDIAN, 0, ItemIds::OBSIDIAN), "Obsidian", new BlockBreakInfo(35.0 /* 50 in PC */, BlockToolType::PICKAXE, ToolTier::DIAMOND()->getHarvestLevel(), 6000.0));
    }

    /*
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
    public function onBreak(Item $item, Player $player = null): bool {
        parent::onBreak($item);
        foreach($this->getAllSides() as $i => $block) {
            if($block instanceof Portal) {
                if($block->getSide(Facing::WEST) instanceof Portal or $block->getSide(Facing::EAST) instanceof Portal) {
                    for($x = $block->getPosition()->getFloorX(); $this->getPosition()->getWorld()->getBlockAt($x, $block->getPosition()->getFloorY(), $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::PORTAL; $x++) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::PORTAL; $y++) {
                                $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                    }
                    for($x = $block->getPosition()->getFloorX() - 1; $this->getPosition()->getWorld()->getBlockAt($x, $block->getPosition()->getFloorY(), $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::PORTAL; $x--) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::PORTAL; $y++) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($x, $y, $block->getPosition()->getFloorZ())->getId() == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($x, $y, $block->getPosition()->getFloorZ()), VanillaBlocks::AIR());
                        }
                    }
                }
                else {
                    for($z = $block->getPosition()->getFloorZ(); $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY(), $z)->getId() == BlockLegacyIds::PORTAL; $z++) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z)->getId() == BlockLegacyIds::PORTAL; $y++) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z)->getId() == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                    }
                    for($z = $block->getPosition()->getFloorZ() - 1; $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY(), $z)->getId() == BlockLegacyIds::PORTAL; $z--) {
                        for($y = $block->getPosition()->getFloorY(); $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z)->getId() == BlockLegacyIds::PORTAL; $y++) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                        for($y = $block->getPosition()->getFloorY() - 1; $this->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $y, $z)->getId() == BlockLegacyIds::PORTAL; $y--) {
                            $this->getPosition()->getWorld()->setBlock(new Vector3($block->getPosition()->getFloorX(), $y, $z), VanillaBlocks::AIR());
                        }
                    }
                }
                return true;
            }
        }
        return true;
    }*/

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if($item->getId() === ItemIds::FLINT_AND_STEEL){
            $affectedBlock = $this->getSide($face);
            if($affectedBlock->getId() === BlockLegacyIds::AIR){
                $world = $player->getWorld();
                $pos = $affectedBlock->getPosition()->asVector3();
                $blocks = $this->fill($world, $pos, 10, Facing::WEST);
                if(count($blocks) === 0){
                    $blocks = $this->fill($world, $pos, 10, Facing::NORTH);
                }
                if(count($blocks) > 0){
                    ($ev = new PlayerCreateNetherPortalEvent($player, $this))->call();
                    if(!$ev->isCancelled()){
                        foreach($blocks as $hash => $block){
                            if($block->getId() === BlockLegacyIds::PORTAL){
                                World::getBlockXYZ($hash, $x, $y, $z);
                                $world->setBlockAt($x, $y, $z, $block, false);
                            }
                        }
                        return true;
                    }
                }
            }
        }
        return parent::onInteract($item, $face, $clickVector, $player); // TODO: Change the autogenerated stub
    }

    public function fill(World $world, Vector3 $origin, int $radius, int $direction) : array{
        $blocks = [];

        $visits = new SplQueue();
        $visits->enqueue($origin);
        while(!$visits->isEmpty()){
            /** @var Vector3 $coordinates */
            $coordinates = $visits->dequeue();
            if($origin->distanceSquared($coordinates) >= $this->lengthSquared){
                return [];
            }

            $coordinates_hash = World::blockHash($coordinates->x, $coordinates->y, $coordinates->z);
            $block = $world->getBlockAt($coordinates->x, $coordinates->y, $coordinates->z);

            if(
                $block->getId() === BlockLegacyIds::AIR &&
                HCFUtils::firstOrDefault(
                    $blocks,
                    static function(int $hash, Block $block) use($coordinates_hash) : bool{ return $hash === $coordinates_hash; }
                ) === null
            ){
                $this->visit($coordinates, $blocks, $direction);
                if($direction === Facing::WEST){
                    $visits->enqueue($coordinates->getSide(Facing::NORTH));
                    $visits->enqueue($coordinates->getSide(Facing::SOUTH));
                }elseif($direction === Facing::NORTH){
                    $visits->enqueue($coordinates->getSide(Facing::WEST));
                    $visits->enqueue($coordinates->getSide(Facing::EAST));
                }
                $visits->enqueue($coordinates->getSide(Facing::UP));
                $visits->enqueue($coordinates->getSide(Facing::DOWN));
            }elseif(!$this->isValid($block, $coordinates_hash, $blocks)){
                return [];
            }
        }

        return $blocks;
    }

    public function visit(Vector3 $coordinates, array &$blocks, int $direction) : void{
        $blocks[World::blockHash($coordinates->x, $coordinates->y, $coordinates->z)] = BlockFactory::getInstance()->get(BlockLegacyIds::PORTAL, $direction - 2);
    }

    private function isValid(Block $block, int $coordinates_hash, array $portals) : bool{
        return $block->getId() === BlockLegacyIds::OBSIDIAN ||
            HCFUtils::firstOrDefault(
                $portals,
                static function(int $hash, Block $b) use($coordinates_hash) : bool{ return $hash === $coordinates_hash && $b->getId() === BlockLegacyIds::PORTAL; }
            ) !== null;
    }
}