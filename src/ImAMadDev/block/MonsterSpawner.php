<?php

namespace ImAMadDev\block;

use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\MonsterSpawner as VanillaMonsterSpawner;
use pocketmine\block\tile\Tile;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\BlockTransaction;

class MonsterSpawner extends VanillaMonsterSpawner {
    /** @var int */
    private int $mobType;
    
    public const TAG_MOB_TYPE = "MobType";
    
    public const ENDERMAN = 38;
    public const CREEPER = 33;
    public const BLAZE = 43;
    public const COW = 11;

    /**
     * Generator constructor.
     *
     * @param int $meta
     */
    public function __construct(int $meta = 0){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::MOB_SPAWNER, 0, ItemIds::MONSTER_SPAWNER, \ImAMadDev\tile\MonsterSpawner::class), "Monster Spawner", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
        $this->mobType = $meta;
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
        return parent::onInteract($item, $face, $clickVector, $player);
    }

    /**
     * @param BlockTransaction $tx
     * @param Item $item
     * @param Block $blockReplace
     * @param Block $blockClicked
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     *
     * @return bool
     */
    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        $this->getPosition()->getWorld()->setBlock($this->getPosition(), $this, true);
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        $mobType = $item->getNamedTag()->getInt(self::TAG_MOB_TYPE, 11);
        if(!$tile instanceof \ImAMadDev\tile\MonsterSpawner) {
            $tile = new \ImAMadDev\tile\MonsterSpawner($this->getPosition()->getWorld(), $this->getPosition()->asVector3());
            $this->getPosition()->getWorld()->addTile($tile);
            $this->setMobType($mobType);
        }
        if($player !== null) {
       	 $item = $player->getInventory()->getItemInHand();
			$item->setCount($item->getCount() - 1);
			$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
		}
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onBreak(Item $item, Player $player = null): bool {
        return parent::onBreak($item, $player);
    }

    /**
     * @return int
     */
    public function getXpDropAmount(): int {
        return 0;
    }

    /**
     * @param Item $item
     *
     * @return Item[]
     */
    public function getDrops(Item $item): array {
        $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
        $drop = ItemFactory::air();
        if($tile instanceof \ImAMadDev\tile\MonsterSpawner) {
            $drop = ItemFactory::getInstance()->get(VanillaBlocks::MONSTER_SPAWNER()->getId(), 0, 1);
            $drop->setCustomName(TextFormat::RESET . TextFormat::GREEN . $tile->getMobName() . " Spawner");
            $nbt = new CompoundTag();
            $nbt->setTag(self::TAG_MOB_TYPE, new IntTag($tile->getMob()));
            $drop->setCustomBlockData($nbt);
   	}
        return [$drop];
    }
    
    #[Pure] private function getMobName(): string {
        return match ($this->getMobType()) {
            self::ENDERMAN => "Enderman",
            self::CREEPER => "Creeper",
            self::COW => "Cow",
            self::BLAZE => "Blaze",
            default => "Cow",
        };
    }
    
    public function getMobByName(string $name): int {
    	switch($name){
    		case "Enderman":
    			return self::ENDERMAN;
    		break;
    		case "Creeper":
        		return self::CREEPER;
    		break;
    		case "Cow":
        		return self::COW;
    		break;
    		case "Blaze":
        		return self::BLAZE;
    		break;
    	}
    }

    /**
     * @return Item
     */
    public function getMobType(): int {
        return $this->mobType;
    }
    
    public function setMobType(int $id): void{
        $this->mobType = $id;
    }

    public function onScheduledUpdate(): void
    {
        parent::onScheduledUpdate(); // TODO: Change the autogenerated stub
    }
}
