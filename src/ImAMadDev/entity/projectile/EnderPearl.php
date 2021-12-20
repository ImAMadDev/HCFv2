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
use pocketmine\world\Position;
use pocketmine\item\{ItemFactory, ItemIds};
use pocketmine\block\{FenceGate, Slab};
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
        $owning = $this->getOwningEntity();
        if($owning instanceof HCFPlayer) {
            if (!$owning->isOnline()) {
                $this->kill();
                return;
            }
            if ($this->isInHitbox($this->position->x, $this->position->y, $this->position->z)){
                $this->kill();
                if ($owning->getCooldown()->has('enderpearl')) {
                    $owning->getCooldown()->remove('enderpearl');
                }
                $owning->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1));
                $owning->sendTip(TextFormat::YELLOW . "Your EnderPearl was returned, to avoid glitching");
                return;
            }
            if ($this->isFence()) {
                $this->kill();
                if ($owning->getCooldown()->has('enderpearl')) {
                    $owning->getCooldown()->remove('enderpearl');
                }
                $owning->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1));
                $owning->sendTip(TextFormat::YELLOW . "Your EnderPearl was returned, to avoid glitching");
                return;
            }
            $claim = ClaimManager::getInstance()->getClaimNameByPosition($owning->getPosition());
            if ($owning->getCooldown()->has('combattag') && stripos($claim, "Spawn") !== false) {
                $this->kill();
                if ($owning->getCooldown()->has('enderpearl')) {
                    $owning->getCooldown()->remove('enderpearl');
                }
                $owning->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1));
                $owning->sendTip(TextFormat::YELLOW . "Your EnderPearl was returned, to avoid glitching");
            }
            if ($this->getPosition()->y > 0) {
                $this->getWorld()->addSound($owning->getPosition(), new EndermanTeleportSound());

                $owning->teleport($this->getPositionPlayer());
                $owning->attack(new EntityDamageEvent($owning, EntityDamageEvent::CAUSE_FALL, 2));

                $this->getWorld()->addSound($owning->getPosition(), new EndermanTeleportSound());
                if ($this->isPearling()) {
                    $direction = $owning->getDirectionVector()->multiply(3);
                    if ($this->isInHitbox($direction->x, $direction->y + 1, $direction->z)){
                        $this->kill();
                        if ($owning->getCooldown()->has('enderpearl')) {
                            $owning->getCooldown()->remove('enderpearl');
                        }
                        $owning->getInventory()->addItem(ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1));
                        $owning->sendTip(TextFormat::YELLOW . "Your EnderPearl was returned, to avoid glitching");
                        return;
                    }
                    $this->getWorld()->addSound($owning->getPosition(), new EndermanTeleportSound());

                    $owning->teleport(Position::fromObject($owning->getPosition()->add($direction->x, (int)$direction->y + 1, $direction->z), $owning->getWorld()));
                    $owning->attack(new EntityDamageEvent($this->getOwningEntity(), EntityDamageEvent::CAUSE_FALL, 2));

                    $this->getWorld()->addSound($owning->getPosition(), new EndermanTeleportSound());
                }
            }
            $this->kill();
        }
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
     * @param float $x
     * @param float $y
     * @param float $z
     * @return bool
     */
    public function isInHitbox(float $x, float $y, float $z): bool
    {
        if(!isset($this->getPosition()->getWorld()->getBlockAt((int)$x, (int)$y, (int)$z)->getCollisionBoxes()[0])) return False;
        foreach ($this->getPosition()->getWorld()->getBlockAt((int)$x, (int)$y, (int)$z)->getCollisionBoxes() as $blockHitBox) {
           if($x < 0) $x = $x + 1;
           if($z < 0) $z = $z + 1;
            if (($blockHitBox->minX < $x) AND ($x < $blockHitBox->maxX) AND ($blockHitBox->minY < $y) AND ($y < $blockHitBox->maxY) AND ($blockHitBox->minZ < $z) AND ($z < $blockHitBox->maxZ)) return True;
        }
      return False;
    }

    public function isInSolid(float $x, float $y, float $z): bool
    {
        $block = $this->getWorld()->getBlockAt((int) floor($x), (int) floor($y), (int) floor($z));
        return $block->isSolid() and !$block->isTransparent() and $block->collidesWithBB($this->getBoundingBox());
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

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::ENDER_PEARL;
    }
}