<?php

namespace ImAMadDev\entity\projectile;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\{Position, World};
use pocketmine\item\{Item, ItemFactory, ItemIds};
use pocketmine\block\{BlockLegacyIds, FenceGate, Slab};
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\world\sound\EndermanTeleportSound;


use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;

class EnderPearl extends Throwable {

    /** @var Vector3|null */
    protected ?Vector3 $position = null;

	/* 
    public $width = 0.10;
    public $length = 0.10;
    public $height = 0.10;
    
    protected $gravity = 0.1, $drag = 0.05;*/
    
   /** @var int|float */
    public int|float $width = 0.30;
    public float $length = 0.35;
    public int|float $height = 0.35;
    

    /**
     * EnderPearl Constructor.
     * @param Location $location
     * @param Entity|null $shootingEntity
     * @param CompoundTag|null $nbt
     */
    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null){
        $this->gravity = 0.08;
        $this->drag = 0.01;
        parent::__construct($location, $shootingEntity, $nbt);
    }

    /**
     * @return void
     */
    protected function teleportAt() : void {
        if(!$this->getOwningEntity() instanceof HCFPlayer||!$this->getOwningEntity()->isOnline()){
            $this->kill();
            return;
        }
        if($this->getOwningEntity() instanceof HCFPlayer && $this->isFence()){
			$this->kill();
			if($this->getOwningEntity()->getCooldown()->has('enderpearl')) {
				$this->getOwningEntity()->getCooldown()->remove('enderpearl');
			}
			$this->getOwningEntity()->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1));
			$this->getOwningEntity()->sendTip(TextFormat::YELLOW."Your EnderPearl was returned, to avoid glitching");
			return;
		}
		$claim = ClaimManager::getInstance()->getClaimByPosition($this->getPosition()) == null ? "Wilderness" : ClaimManager::getInstance()->getClaimByPosition($this->getPosition())->getName();
		if($this->getOwningEntity() instanceof HCFPlayer && $this->getOwningEntity()->getCooldown()->has('combattag') && stripos($claim, "Spawn") !== false){
			$this->kill();
			if($this->getOwningEntity()->getCooldown()->has('enderpearl')) {
				$this->getOwningEntity()->getCooldown()->remove('enderpearl');
			}
			$this->getOwningEntity()->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1));
			$this->getOwningEntity()->sendTip(TextFormat::YELLOW."Your EnderPearl was returned, to avoid glitching");
			return;
		}
		if($this->getPosition()->y > 0){
			$this->getWorld()->addSound($this->getOwningEntity()?->getPosition(), new EndermanTeleportSound());
			
			$this->getOwningEntity()->teleport($this->getPositionPlayer());
			$this->getOwningEntity()->attack(new EntityDamageEvent($this->getOwningEntity(), EntityDamageEvent::CAUSE_FALL, 2));
			
			$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());
            if($this->isPearling()){
				$direction = $this->getOwningEntity()->getDirectionVector()->multiply(3);
				$this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());
				
                $this->getOwningEntity()->teleport(Position::fromObject($this->getOwningEntity()->getPosition()->add($direction->x, (int)$direction->y + 1, $direction->z), $this->getOwningEntity()->getWorld()));
                $this->getOwningEntity()->attack(new EntityDamageEvent($this->getOwningEntity(), EntityDamageEvent::CAUSE_FALL, 2));
                
                $this->getWorld()->addSound($this->getOwningEntity()->getPosition(), new EndermanTeleportSound());
            }
		}
		$this->kill();
	}

    /**
     * @return void
     */
    protected function readPosition() : void {
        $new = $this->getPosition();
        if($new->distanceSquared($this->getPositionPlayer()) > 1){
            $this->setPositionPlayer(new Vector3($this->getPosition()->x, (int)$this->getPosition()->y, $this->getPosition()->z));
        }
	}
	
	/**
     * @param Vector3 $position
     */
    protected function setPositionPlayer(Vector3 $position){
        $this->position = $position;
    }

    /**
     * @return Vector3
     */
    #[Pure] protected function getPositionPlayer() : Vector3 {
        return $this->position === null ? new Vector3(0, 0, 0) : $this->position;
    }

    /**
	 * @return bool
	 */
	public function isFence() : bool {
		for($x = ((int)$this->getPosition()->x); $x <= ((int)$this->getPosition()->x); $x++){
			for($z = ((int)$this->getPosition()->z); $z <= ((int)$this->getPosition()->z); $z++){
				$block = $this->getWorld()->getBlockAt($x, $this->getPosition()->y, $z);
				if($block instanceof FenceGate){
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
    }
    
    /**
	 * @return bool
	 */
	public function isPearling() : bool {
		for($x = ($this->getPosition()->x + 0.1); $x <= ($this->getPosition()->x - 0.1); $x++){
			for($z = ($this->getPosition()->z + 0.1); $z <= ($this->getPosition()->z - 0.1); $z++){
				$block = $this->getWorld()->getBlockAt($x, $this->getPosition()->y, $z);
				if($block instanceof Slab){
					return true;
				}else{
					return false;
				}
			}
		}
		return false;
	}

    /** 
	 * @param Int $currentTick
	 * @return bool
	 */
	public function onUpdate(int $currentTick) : bool {
		if($this->closed){
			return false;
		}
		$this->readPosition();
		
		$this->timings->startTiming();
		$hasUpdate = parent::onUpdate($currentTick);
		
		if($this->isCollided){
			$this->teleportAt();
			$hasUpdate = true;
		}
		$this->timings->stopTiming();
		return $hasUpdate;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::ENDER_PEARL;
    }
}