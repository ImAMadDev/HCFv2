<?php

namespace ImAMadDev\block;

use ImAMadDev\manager\ClaimManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\tile\MonsterSpawner as MonsterSpawnerClass;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\MonsterSpawner as VanillaMonsterSpawner;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class MonsterSpawner extends VanillaMonsterSpawner {
    /** @var int */
    private int $mobType;
    
    public const TAG_MOB_TYPE = "MobType";
    
    public const ENDERMAN = EntityLegacyIds::ENDERMAN;
    public const CREEPER = EntityLegacyIds::CREEPER;
    public const BLAZE = EntityLegacyIds::BLAZE;
    public const COW = EntityLegacyIds::COW;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::MOB_SPAWNER, 0, ItemIds::MONSTER_SPAWNER, MonsterSpawnerClass::class), "Monster Spawner", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
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
        $drop = ItemFactory::getInstance()->get($this->getIdInfo()->getItemId(), 0, 1);
        $drop->setCustomName(TextFormat::RESET . TextFormat::GREEN . $this->getMobName() . " Spawner");
        $drop->setLore([TextFormat::LIGHT_PURPLE . "Put a chest on top of the generator to collect positions."]);
        $nbt = new CompoundTag();
        $nbt->setTag(self::TAG_MOB_TYPE, new IntTag($this->getMobType()));
        $drop->setCustomBlockData($nbt);
        return [$drop];
    }
    
    #[Pure] private function getMobName(): string {
        return match ($this->getMobType()) {
            self::ENDERMAN => "Enderman",
            self::CREEPER => "Creeper",
            self::BLAZE => "Blaze",
            default => "Cow",
        };
    }
    
    public function getMobByName(string $name): int {
        return match ($name) {
            "Enderman" => self::ENDERMAN,
            "Creeper" => self::CREEPER,
            "Blaze" => self::BLAZE,
            default => self::COW,
        };
    }

    /**
     * @return int
     */
    public function getMobType(): int {
        return $this->mobType;
    }
    
    public function setMobType(int $id): void{
        $this->mobType = $id;
    }

    public function onScheduledUpdate(): void
    {
        $tile = $this->position->getWorld()->getTile($this->position);
        if($tile instanceof MonsterSpawnerClass and $tile->onUpdate()){
            $this->updateBlock();
        }
    }

    protected function canRescheduleTransferCooldown() : bool{
        return true;
    }

    public function updateBlock()
    {
        if ($this->canRescheduleTransferCooldown()){
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 20);
        }
    }

    public function onNearbyBlockChange() : void{
        parent::onNearbyBlockChange();
        $this->updateBlock();
    }

    public function readStateFromWorld() : void{
        parent::readStateFromWorld();
        $this->updateBlock();
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if(!$item->isNull() && $player instanceof HCFPlayer) {
            $mob = $this->selectMob($item);
            $claim = ClaimManager::getInstance()->getClaimByPosition($this->getPosition());
            if($claim !== null) {
                if($claim->canEdit($player->getFaction())) {
                    if ($mob !== 0) {
                        $this->setMobType($mob);
                        $player->getInventory()->removeItem(ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, $mob, 1));
                    }
                }
            } else {
                $this->setMobType($mob);
                $player->getInventory()->removeItem(ItemFactory::getInstance()->get(ItemIds::SPAWN_EGG, $mob, 1));
            }
        }
        return parent::onInteract($item, $face, $clickVector, $player);
    }

    public function selectMob(Item $item) : int
    {
        if ($item->getId() === ItemIds::SPAWN_EGG){
            match ($item->getMeta()){
              self::ENDERMAN => EntityLegacyIds::ENDERMAN,
              self::CREEPER => EntityLegacyIds::CREEPER,
              self::BLAZE => EntityLegacyIds::BLAZE,
              self::COW => EntityLegacyIds::COW,
              default => 0
            };
        }
        return 0;
    }
}
