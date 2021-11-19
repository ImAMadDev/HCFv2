<?php

namespace ImAMadDev\tile;

use pocketmine\block\Block;
use ImAMadDev\inventory\ShulkerBoxInventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Container;
use pocketmine\tile\Nameable;
use pocketmine\tile\NameableTrait;
use pocketmine\tile\ContainerTrait;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class ShulkerBox extends Spawnable implements InventoryHolder, Container, Nameable
{
    use NameableTrait {
        addAdditionalSpawnData as addNameSpawnData;
    }
    use ContainerTrait;

    public const TAG_FACING = "facing";
    public const TAG_UNDYED = "isUndyed";

    /** @var int */
    protected $facing = Vector3::SIDE_UP;
    /** @var bool */
    protected $isUndyed = true;

    /** @var ShulkerBoxInventory */
    protected $inventory;

    /**
     * @param int $facing
     */
    public function setFacing(int $facing) : void
    {
        if ($facing < 0 or $facing > 5) {
            throw new \InvalidArgumentException("Invalid shulkerbox facing: $facing");
        }

        $this->facing = $facing;
        $this->onChanged();
    }

    /**
     * @return int
     */
    public function getFacing() : int
    {
        return $this->facing;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return 27;
    }

    /**
     * @return string
     */
    public function getDefaultName() : string
    {
        return "Shulker Box";
    }

    /**
     * @return ShulkerBoxInventory
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @return ShulkerBoxInventory
     */
    public function getRealInventory()
    {
        return $this->inventory;
    }

    protected function readSaveData(CompoundTag $nbt) : void
    {
        $this->facing = $nbt->getByte(self::TAG_FACING, Vector3::SIDE_DOWN);
        $this->isUndyed = $nbt->getByte(self::TAG_UNDYED, 1) == 1;

        $this->inventory = new ShulkerBoxInventory($this);

        $this->loadName($nbt);
        $this->loadItems($nbt);
    }

    protected function writeSaveData(CompoundTag $nbt) : void
    {
        $nbt->setTag(new ByteTag(self::TAG_FACING, $this->facing));
        $nbt->setTag(new ByteTag(self::TAG_UNDYED, $this->isUndyed ? 1 : 0));

        $this->saveName($nbt);
        $this->saveItems($nbt);
    }

    public function writeBlockData(CompoundTag $nbt)
    {
        $this->saveName($nbt);
        $this->saveItems($nbt);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt) : void
    {
        $nbt->setTag(new ByteTag(self::TAG_FACING, $this->facing));
        $nbt->setTag(new ByteTag(self::TAG_UNDYED, $this->isUndyed ? 1 : 0));

        $this->addNameSpawnData($nbt);
    }

    protected static function createAdditionalNBT(CompoundTag $nbt, Vector3 $pos, ?int $face = null, ?Item $item = null, ?Player $player = null) : void
    {
        parent::createAdditionalNBT($nbt, $pos, $face, $item, $player);

        $nbt->setByte(self::TAG_FACING, $face ?? Vector3::SIDE_DOWN);
        if ($item !== null) {
            $nbt->setByte(self::TAG_UNDYED, $item->getId() == Block::UNDYED_SHULKER_BOX ? 1 : 0);
        }
    }

    public function close() : void
    {
        if (!$this->closed) {
            $this->inventory->removeAllViewers(true);
            $this->inventory = null;
            parent::close();
        }
    }
}
