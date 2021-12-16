<?php

namespace ImAMadDev\tile;

use ImAMadDev\entity\mobs\Blaze;
use ImAMadDev\entity\mobs\Cow;
use ImAMadDev\entity\mobs\Creeper;
use ImAMadDev\entity\mobs\Enderman;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Hopper;
use pocketmine\block\tile\TileFactory;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\block\tile\Spawnable;

use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\Utils;
use pocketmine\world\World;

class MonsterSpawner extends Spawnable {
	
	public const TAG_MOB_TYPE = "MobType";
	private int $mobType = EntityLegacyIds::COW;
    private int $generateTick = 0;

    /**
     * @param World $world
     * @param Vector3 $pos
     */
    public function __construct(World $world, Vector3 $pos)
    {
		parent::__construct($world, $pos);
        $world->scheduleDelayedBlockUpdate($pos, 20);
	}
	
	public function getMob(): int
    {
        return $this->mobType;
    }

    #[Pure] public function getMobName(): string {
        return match ($this->getMob()) {
            38 => "Enderman",
            33 => "Creeper",
            43 => "Blaze",
            default => "Cow",
        };
    }

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void {
        if($nbt->getTag(self::TAG_MOB_TYPE) instanceof IntTag) {
            $this->mobType = $nbt->getInt(self::TAG_MOB_TYPE);
        }
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setInt(self::TAG_MOB_TYPE, $this->mobType);
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function addAdditionalSpawnData(CompoundTag $nbt): void
    {
        $nbt->setTag(self::TAG_MOB_TYPE, new IntTag($this->getMob()));
    }

    protected function spawnMob() : void
    {
        switch ($this->getMobName()){
            case 'Enderman':
                $entity = new Enderman($this->getRandomLocation());
                $entity->spawnToAll();
                break;
            case 'Cow':
                $entity = new Cow($this->getRandomLocation());
                $entity->spawnToAll();
                break;
            case 'Creeper':
                $entity = new Creeper($this->getRandomLocation());
                $entity->spawnToAll();
                break;
            case 'Blaze':
                $entity = new Blaze($this->getRandomLocation());
                $entity->spawnToAll();
                break;
        }
    }

    public function canUpdate(): bool
    {
        if (!$this->getPosition()->getWorld()->isChunkLoaded($this->getPosition()->getX() >> 4, $this->getPosition()->getZ() >> 4)) {
            return false;
        }
        if ($this->getMob() === 0) {
            return false;
        }

        if ($this->getPosition()->getWorld()->getNearestEntity($this->getPosition(), 25, Human::class) instanceof HCFPlayer) {
            return true;
        }
        return false;
    }

    private function getRandomLocation() : Location
    {
        $pos = $this->getPosition()->add(mt_rand() / mt_getrandmax() * 4, mt_rand(-1, 1), mt_rand() / mt_getrandmax() * 4);
        $target = $this->getPosition()->getWorld()->getBlock($pos);
        $target2 = $this->getPosition()->getWorld()->getBlock($pos->add(0, 1, 0));
        if ($target->getId() == BlockLegacyIds::AIR and $target2->getId() == BlockLegacyIds::AIR) {
            return Location::fromObject($target->getPosition()->add(0.5, 0, 0.5), $this->getPosition()->getWorld(), lcg_value() * 360, 0);
        }
        return Location::fromObject($this->getPosition()->add(0, 1, 0), $this->getPosition()->getWorld(),lcg_value() * 360, 0);
    }

    public function onUpdate() : bool
    {
        if($this->isClosed()){
            return false;
        }
        if (!$this->canUpdate()){
            return false;
        }
        $this->timings->startTiming();
        if($this->generateTick++ >= 120) {
            $this->spawnMob();
            $this->generateTick = 0;
        }
        $this->timings->stopTiming();
        return true;
    }
}