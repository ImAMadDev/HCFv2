<?php

namespace ImAMadDev\block;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;

use pocketmine\math\Facing;

use pocketmine\block\{Block,
    Glass,
    Door,
    SimplePressurePlate};
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class PressurePlate extends SimplePressurePlate {

    /**
     * @return bool
     */
    public function isSolid() : bool {
        return false;
    }
    
    /**
     * @return bool
     */
    public function hasEntityCollision() : bool {
        return true;
    }

    /**
     * @param BlockTransaction $tx
     * @param Item $item
     * @param Block $blockReplace
     * @param Block $blockClicked
     * @param Int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     * @return bool
     */
    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {
		if(!$blockClicked->isSolid()||$blockClicked instanceof Glass){
			return false;
        }
		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function onEntityInside(Entity $entity) : bool {
        if(!$entity instanceof HCFPlayer) {
            return false;
        }
        $claim = ClaimManager::getInstance()->getClaimByPosition($this->getPosition());
		if($claim !== null) {
			if(!$claim->canEdit($entity->getFaction())) {
				return false;
			}
		}
        $damage = $this->nearbyEntities();
        if($this->getMeta() !== $damage){
            foreach($this->getSidesHCF(2) as $block){
                if($block instanceof Door) {
                    $block->setOpen(!$block->isOpen());
                }
            }
            $this->setPressed($this->isActivated());
        }
        $this->getPosition()->getWorld()->scheduleDelayedBlockUpdate($this->getPosition(), 20);
        return true;
    }

    /**
     * @return void
     */
    public function onScheduledUpdate() : void {
        $damage = $this->nearbyEntities();
        if($this->getMeta() !== $damage){
            foreach($this->getSidesHCF(2) as $block){
                if($block instanceof Door) $block->setOpen(!$block->isOpen());
            }
            $this->setPressed($this->isActivated());
        }
        if($damage > 0){
            $this->getPosition()->getWorld()->scheduleDelayedBlockUpdate($this->getPosition(), 20);
        }
    }

    /**
     * @return int
     */
    protected function nearbyEntities() : int {
        $value = count($this->getPosition()->getWorld()->getNearbyEntities($this->box()));
        return $value > 0 ? 1 : 0;
    }

    /**
     * @return AxisAlignedBB
     */
    protected function box() : AxisAlignedBB {
        return new AxisAlignedBB(
            $this->getPosition()->x + 0.0625,
            $this->getPosition()->y,
            $this->getPosition()->z + 0.0625,
            $this->getPosition()->x + 0.9375,
            $this->getPosition()->y + 0.0625,
            $this->getPosition()->z + 0.9375,
        );
    }

    public function isActivated(): bool
    {
        return !(($this->nearbyEntities() == 0));
    }


    /**
     * @return void
     */
    public function onNearbyBlockChange() : void {
        $under = $this->getSide(Facing::DOWN);
        if ($under->isSolid() && !$under->isTransparent()) {
            return;
        }
        $this->getPosition()->getWorld()->useBreakOn($this->getPosition());
    }
	
	public function getHorizontalSidesHCF(int $step) : array{
		return [
			$this->getSide(Facing::NORTH, $step),
			$this->getSide(Facing::SOUTH, $step),
			$this->getSide(Facing::WEST, $step),
			$this->getSide(Facing::EAST, $step)
		];
	}
    
    public function getSidesHCF(int $step) : array{
		return array_merge(
			[
				$this->getSide(Facing::DOWN, $step),
				$this->getSide(Facing::UP, $step)
			],
			$this->getHorizontalSidesHCF($step)
		);
	}
}