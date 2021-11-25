<?php

namespace ImAMadDev\block;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\tile\PotionGenerator as PotionGeneratorClass;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType;
use pocketmine\block\BrewingStand;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\Hopper;
use pocketmine\block\tile\Tile;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ImAMadDev\manager\{ClaimManager, FormManager};
use pocketmine\world\BlockTransaction;

class PotionGenerator extends BrewingStand {
    /** @var int */
    private int $potionType;
    
    public const REGENERATION = 22;
    public const LONG_SWIFTNESS = 15;
    public const STRONG_SWIFTNESS = 16;
    public const LONG_INVISIBILITY = 8;
    public const LONG_FIRE_RESISTANCE = 13;
    public const STRONG_POISON = 25;
    public const NIGHT_VISION = 5;
    
    public const POTION_TAG = "Potion";

    public function __construct(){
        parent::__construct(new BID(Ids::BREWING_STAND_BLOCK, 0, ItemIds::BREWING_STAND, PotionGeneratorClass::class), "Brewing Stand", new BlockBreakInfo(0.5, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
		$this->potionType = self::REGENERATION;
	}

    public function onNearbyBlockChange() : void{
        parent::onNearbyBlockChange();
        $this->updateBlock();
    }

    public function getContainerDown() : ?Container{
        $above = $this->position->getWorld()->getTileAt($this->position->x, $this->position->y - 1, $this->position->z);
        return $above instanceof Container ? $above : null;
    }

    protected function canRescheduleTransferCooldown() : bool{
        return $this->getContainerDown() !== null;
    }

    public function updateBlock()
    {
        if ($this->canRescheduleTransferCooldown()){
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 25);
        }
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool
    {
        if($item->isNull() && $player instanceof HCFPlayer) {
            $claim = ClaimManager::getInstance()->getClaimByPosition($this->getPosition());
            if($claim !== null) {
                if($claim->canEdit($player->getFaction())) {
                    FormManager::getGeneratorForm($player, $this);
                }
            } else {
                FormManager::getGeneratorForm($player, $this);
            }
            $this->updateBlock();
        }
        return true;
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
        $drop->setCustomName(TextFormat::RESET . TextFormat::GOLD . $this->getPotionName() . " Generator");
        $drop->setLore([TextFormat::LIGHT_PURPLE . "Put a chest on top of the generator to collect positions."]);
        $nbt = CompoundTag::create();
        $nbt->setInt(self::POTION_TAG, $this->getPotionType());
        $drop->setCustomBlockData($nbt);
        return [$drop];
    }
    
    #[Pure] private function getPotionName(): string {
        return match ($this->getPotionType()) {
            self::REGENERATION => "Regeneration",
            self::LONG_SWIFTNESS => "Long Swiftness",
            self::STRONG_SWIFTNESS => "Strong Swiftness",
            self::LONG_INVISIBILITY => "Long Invisibility",
            self::STRONG_POISON => "Poison",
            self::LONG_FIRE_RESISTANCE => "Long Fire Resistance",
            self::NIGHT_VISION => "Night Vision",
            default => 'Regeneration',
        };
    }
    
    public function getPotionByName(string $name): int {
        return match ($name) {
            "Long Swiftness" => self::LONG_SWIFTNESS,
            "Strong Swiftness" => self::STRONG_SWIFTNESS,
            "Long Invisibility" => self::LONG_INVISIBILITY,
            "Poison" => self::STRONG_POISON,
            "Long Fire Resistance" => self::LONG_FIRE_RESISTANCE,
            "Night Vision" => self::NIGHT_VISION,
            default => self::REGENERATION,
        };
    }

    /**
     * @return int
     */
    public function getPotionType(): int {
        return $this->potionType;
    }
    
    public function setPotionType(int $id): void{
        $this->potionType = $id;
    }

    public function readStateFromWorld() : void{
        parent::readStateFromWorld();
        $this->updateBlock();
    }

    public function getTile() : ?Tile
    {
        return $this->getPosition()->getWorld()->getTile($this->getPosition());
    }

    public function onScheduledUpdate(): void
    {
        $tile = $this->position->getWorld()->getTile($this->position);
        if($tile instanceof PotionGeneratorClass and $tile->onUpdate()){
            $this->updateBlock();
        }
    }
}
