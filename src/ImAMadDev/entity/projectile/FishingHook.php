<?php

namespace ImAMadDev\entity\projectile;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;

class FishingHook extends Projectile {

	/** @var float */
    public float $length = 0.25;
    public float $height = 0.25;
    public float $width = 0.25;

    /*
    protected float $drag = 0.01;
    protected float $gravity = 0.11;*/

    /** @var bool */
    protected bool $hasFishing = false;

    /**
     * FishingHook Constructor.
     * @param Location $location
     * @param CompoundTag $nbt
     * @param Entity|null $shootingEntity
     */
    public function __construct(Location $location, CompoundTag $nbt, ?Entity $shootingEntity = null){
        parent::__construct($location, $shootingEntity, $nbt);
    }

    /** 
	 * @param Int $currentTick
	 * @return bool
	 */
	public function onUpdate(int $currentTick) : bool {
		if($this->closed){
			return false;
        }

        $this->timings->startTiming();
		$hasUpdate = parent::onUpdate($currentTick);
		
		if($this->isCollided){
			$this->close();
            $hasUpdate = true;
		}
		$this->timings->stopTiming();
        return $hasUpdate;
    }

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::FISHING_HOOK;
    }
}