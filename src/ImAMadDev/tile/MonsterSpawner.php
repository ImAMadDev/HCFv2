<?php

namespace ImAMadDev\tile;

use pocketmine\block\tile\TileFactory;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\block\tile\Spawnable;

use pocketmine\nbt\tag\IntTag;
use pocketmine\world\World;

class MonsterSpawner extends Spawnable {
	
	public const TAG_MOB_TYPE = "MobType";
	private int $mobType = 11;

    /**
     * @param World $world
     * @param Vector3 $vector3
     */
    public function __construct(World $world, Vector3 $vector3)
    {
		parent::__construct($world, $vector3);
	}
	
	public function getMob(): int
    {
        return $this->mobType;
    }

    public function getMobName(): string {
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
        if($nbt->getCompoundTag(self::TAG_MOB_TYPE) instanceof CompoundTag) {
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

    public function saveNBT() : CompoundTag{
        $nbt = CompoundTag::create()
            ->setInt(self::TAG_X, $this->position->getFloorX())
            ->setInt(self::TAG_Y, $this->position->getFloorY())
            ->setInt(self::TAG_Z, $this->position->getFloorZ());
        $this->writeSaveData($nbt);

        return $nbt;
    }
}