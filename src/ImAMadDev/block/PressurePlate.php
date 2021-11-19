<?php

namespace ImAMadDev\block;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;

use pocketmine\Player;

use pocketmine\block\{Block, BlockFactory, Glass, Transparent, FenceGate, Door, BlockToolType};
use pocketmine\item\{Item, TieredTool};
use pocketmine\math\Vector3;
use pocketmine\math\AxisAlignedBB;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\level\sound\DoorSound;

class PressurePlate extends Transparent {

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
     * @return bool
     */
    public function canPassThrough() : bool {
        return true;
    }

    /**
     * @return float
     */
    public function getHardness() : float {
        return 0.5;
    }

    /**
     * @return Int
     */
    public function getVariantBitmask() : int {
        return 0;
    }

    /**
     * @return Int
     */
    public function getToolType() : int {
        return BlockToolType::TYPE_PICKAXE;
    }

    /**
     * @return Int
     */
    public function getToolHarvestLevel() : int {
        return TieredTool::TIER_WOODEN;
    }

    /**
	 * @param Item $item
	 * @param Block $blockReplace
	 * @param Block $blockClicked
	 * @param Int $face
	 * @param Vector3 $clickVector
	 * @param Player $player
	 * @return bool
	 */
    public function place(Item $item, Block $blockReplace, Block $blockClicked, Int $face, Vector3 $clickVector,Player $player = null) : bool {
		if(!$blockClicked->isSolid()||$blockClicked instanceof Glass){
			return false;
        }
        $this->getWorld()->setBlock($blockReplace, $this, true, true);
		return true;
    }

    /**
     * @param Entity $entity
     * @return void
     */
    public function onEntityCollide(Entity $entity) : void {
        if(!$entity instanceof HCFPlayer) return;
        $claim = ClaimManager::getInstance()->getClaimByPosition($this->asPosition());
		if($claim !== null) {
			if(!$claim->canEdit($entity->getFaction())) {
				return;
			}
		}
        $damage = $this->nearbyEntities();
        if($this->getDamage() !== $damage){
            foreach($this->getSidesHCF(2) as $block){
                if($block instanceof Door){
                	if(($block->getDamage() & 0x08) === 0x08){
                		$down = $block->getSide(Vector3::SIDE_DOWN);
						if($down->getId() === $block->getId()){
							$meta = $down->getDamage() ^ 0x04;
							$this->getWorld()->setBlock($block, $block, true, true);
						}
					}else{
						$block->meta ^= 0x04;
						$this->getWorld()->setBlock($block, $block, true, true);
                    }
                    $this->getWorld()->addSound(new DoorSound($block));
                }
            }
            $this->setDamage($damage);
            $this->getWorld()->setBlock($this, $this, true, true);
        }
        $this->getWorld()->scheduleDelayedBlockUpdate($this, 20);
    }

    /**
     * @return void
     */
    public function onScheduledUpdate() : void {
        $damage = $this->nearbyEntities();
        if($this->getDamage() !== $damage){
            foreach($this->getSidesHCF(2) as $block){
                if($block instanceof Door){
                	if(($block->getDamage() & 0x08) === 0x08){
                		$down = $block->getSide(Vector3::SIDE_DOWN);
						if($down->getId() === $block->getId()){
							$meta = $down->getDamage() ^ 0x04;
							$this->getWorld()->setBlock($block, $block, true, true);
						}
					}else{
						$block->meta ^= 0x04;
						$this->getWorld()->setBlock($block, $block, true, true);
                    }
                    $this->getWorld()->addSound(new DoorSound($block));
                }
            }
            $this->setDamage($damage);
            $this->getWorld()->setBlock($this, $this, true, true);
        }
        if($damage > 0){
            $this->getWorld()->scheduleDelayedBlockUpdate($this, 20);
        }
    }

    /**
     * @return Int
     */
    protected function nearbyEntities() : Int {
        $value = count($this->getWorld()->getNearbyEntities($this->box()));
        return $value > 0 ? 1 : 0;
    }

    /**
     * @return AxisAlignedBB
     */
    protected function box() : AxisAlignedBB {
        return new AxisAlignedBB(
            $this->x + 0.0625,
            $this->y,
            $this->z + 0.0625,
            $this->x + 0.9375,
            $this->y + 0.0625,
            $this->z + 0.9375,
        );
    }
    
    /**
     * @return void
     */
    public function onNearbyBlockChange() : void {
        $under = $this->getSide(Vector3::SIDE_DOWN);
        if ($under->isSolid() && !$under->isTransparent()) {
            return;
        }
        $this->getWorld()->useBreakOn($this);
    }
	
	public function getHorizontalSidesHCF(int $step) : array{
		return [
			$this->getSide(Vector3::SIDE_NORTH, $step),
			$this->getSide(Vector3::SIDE_SOUTH, $step),
			$this->getSide(Vector3::SIDE_WEST, $step),
			$this->getSide(Vector3::SIDE_EAST, $step)
		];
	}
    
    public function getSidesHCF(int $step) : array{
		return array_merge(
			[
				$this->getSide(Vector3::SIDE_DOWN, $step),
				$this->getSide(Vector3::SIDE_UP, $step)
			],
			$this->getHorizontalSidesHCF($step)
		);
	}
}

?>