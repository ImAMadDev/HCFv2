<?php

namespace ImAMadDev\entity\projectile;

use pocketmine\entity\Location;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\timings\Timings;
use pocketmine\math\{Vector3, VoxelRayTrace};

use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;

abstract class NewProjectile extends Projectile {
	
	protected ?Entity $entityHitResult = null;

    /**
     * @param float $dx
     * @param float $dy
     * @param float $dz
     * @return void
     */
    public function move(float $dx, float $dy, float $dz) : void {
        $this->blocksAround = null;

		Timings::$entityMove->startTiming();

		$start = $this->getPosition()->asVector3();
		$end = $start->add($dx, $dy, $dz);

		$blockHit = null;
		$entityHit = null;
		$hitResult = null;

		foreach(VoxelRayTrace::betweenPoints($start, $end) as $vector3){
			$block = $this->getWorld()->getBlockAt($vector3->x, $vector3->y, $vector3->z);

			$blockHitResult = $this->calculateInterceptWithBlock($block, $start, $end);
			if($blockHitResult !== null){
				$end = $blockHitResult->hitVector;
				$blockHit = $block;
				$hitResult = $blockHitResult;
				break;
			}
		}

		$entityDistance = PHP_INT_MAX;

		$newDiff = $end->subtractVector($start);
		foreach($this->getWorld()->getCollidingEntities($this->boundingBox->addCoord($newDiff->x, $newDiff->y, $newDiff->z)->expand(0.2, 0.2, 0.2), $this) as $entity){
			if($entity->getId() === $this->getOwningEntityId() and $this->ticksLived < 5){
				continue;
			}

			$entityBB = $entity->boundingBox->expandedCopy(0.1, 0.1, 0.1);
			$entityHitResult = $entityBB->calculateIntercept($start, $end);

			if($entityHitResult === null){
				continue;
			}

			$distance = $this->location->distanceSquared($entityHitResult->hitVector);

			if($distance < $entityDistance){
				$entityDistance = $distance;
				$entityHit = $entity;
				$hitResult = $entityHitResult;
				$end = $entityHitResult->hitVector;
			}
		}

        $this->location = Location::fromObject(
            $end,
            $this->location->world,
            $this->location->yaw,
            $this->location->pitch
        );
        $this->recalculateBoundingBox();
		if($hitResult !== null){
			/** @var ProjectileHitEvent|null $ev */
			$ev = null;
			if($entityHit !== null){
				$ev = new ProjectileHitEntityEvent($this, $hitResult, $entityHit);
				$this->entityHitResult = $entityHit;
			}elseif($blockHit !== null){
				$ev = new ProjectileHitBlockEvent($this, $hitResult, $blockHit);
			}else{
				assert(false, "unknown hit type");
			}

			if($ev !== null){
				$ev->call();
				$this->onHit($ev);

				if($ev instanceof ProjectileHitEntityEvent){
					$this->onHitEntity($ev->getEntityHit(), $ev->getRayTraceResult());
				}elseif($ev instanceof ProjectileHitBlockEvent){
					$this->onHitBlock($ev->getBlockHit(), $ev->getRayTraceResult());
				}
			}

            $this->isCollided = $this->onGround = true;
            $this->motion = new Vector3(0, 0, 0);
		}else{
            $this->isCollided = $this->onGround = false;
            $this->blockHit = null;

            //recompute angles...
            $f = sqrt(($this->motion->x ** 2) + ($this->motion->z ** 2));
            $this->location->yaw = (atan2($this->motion->x, $this->motion->z) * 180 / M_PI);
            $this->location->pitch = (atan2($this->motion->y, $f) * 180 / M_PI);
		}

        $this->getWorld()->onEntityMoved($this);
        $this->checkBlockIntersections();

        Timings::$entityMove->stopTiming();
    }
}