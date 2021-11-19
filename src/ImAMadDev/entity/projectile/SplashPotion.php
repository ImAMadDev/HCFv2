<?php

namespace ImAMadDev\entity\projectile;

use ImAMadDev\player\HCFPlayer;
use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\item\PotionType;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\color\Color;
use pocketmine\entity\Entity;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;

class SplashPotion extends Throwable {

	public float $length = 0.25;
    public int|float $width = 0.25;
    public int|float $height = 0.25;

    private PotionType $potionType;

    private bool $linger = false;

    public function __construct(Location $location, ?Entity $shootingEntity, PotionType $potionType, ?CompoundTag $nbt = null){
        $this->potionType = $potionType;
        $this->gravity = 0.08;
        $this->drag = 0.5;
		parent::__construct($location, $shootingEntity, $nbt);
	}

    public function saveNBT() : CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setShort("PotionId", PotionTypeIdMap::getInstance()->toId($this->getPotionType()));

        return $nbt;
    }

    public function getResultDamage() : int{
        return -1; //no damage
    }
	
	public function splashOnPlayer() : void
    {
        $radius = 6;
        $effects = $this->getPotionEffects();
        if (count($effects) === 0) {
            $colors = [
                new Color(0x38, 0x5d, 0xc6)
            ];
        } else {
            $colors = [];
            foreach ($effects as $effect) {
                $level = $effect->getEffectLevel();
                for ($j = 0; $j < $level; ++$j) {
                    $colors[] = $effect->getColor();
                }
            }
        }
        $particle = new PotionSplashParticle(Color::mix(...$colors));
        $this->getWorld()->addParticle($this->getPosition(), $particle);
        foreach ($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expand($radius, $radius, $radius)) as $entity) {
            foreach ($this->getPotionEffects() as $effect) {
                if ($entity instanceof HCFPlayer) {
                    $entity->getEffects()->add($effect);
                }
            }
        }
        $this->close();
    }
   
	public function onUpdate(int $currentTick) : bool {
		if($this->closed){
			return false;
		}
		$this->timings->startTiming();
		$hasUpdate = parent::onUpdate($currentTick);
		
		if($this->isCollided){
		    $this->splashOnPlayer();
			$hasUpdate = true;
		}
		$this->timings->stopTiming();
		return $hasUpdate;
    }

    public function getPotionEffects() : array{
        return $this->potionType->getEffects();
    }

    /**
     * @return PotionType
     */
    public function getPotionType(): PotionType
    {
        return $this->potionType;
    }

    public function setPotionType(PotionType $type) : void{
        $this->potionType = $type;
        $this->networkPropertiesDirty = true;
    }

    /**
     * @param Entity $entityHit
     * @param RayTraceResult $hitResult
     * @return void
     */
    protected function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void
    {
    //parent::onHitEntity($entityHit, $hitResult);
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::SPLASH_POTION;
    }

    public function willLinger() : bool{
        return $this->linger;
    }

    protected function syncNetworkData(EntityMetadataCollection $properties) : void{
        parent::syncNetworkData($properties);

        $properties->setShort(EntityMetadataProperties::POTION_AUX_VALUE, PotionTypeIdMap::getInstance()->toId($this->potionType));
        $properties->setGenericFlag(EntityMetadataFlags::LINGER, $this->linger);
    }
}