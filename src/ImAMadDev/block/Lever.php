<?php

namespace ImAMadDev\block;

use pocketmine\block\utils\LeverFacing;
use pocketmine\item\Item;
use pocketmine\math\Axis;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\BlockTransaction;
use pocketmine\world\sound\RedstonePowerOffSound;
use pocketmine\world\sound\RedstonePowerOnSound;
use pocketmine\block\Flowable;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockLegacyMetadata;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\Block;
use pocketmine\block\Door;

class Lever extends Flowable{
	protected LeverFacing $facing;
	protected bool $activated = false;

	public function __construct(){
		$this->facing = LeverFacing::UP_AXIS_X();
		parent::__construct(new BlockIdentifier(BlockLegacyIds::LEVER, 0), "Lever", new BlockBreakInfo(0.5));
	}

	protected function writeStateToMeta() : int{
		$rotationMeta = match($this->facing->id()){
			LeverFacing::DOWN_AXIS_X()->id() => 0,
			LeverFacing::EAST()->id() => 1,
			LeverFacing::WEST()->id() => 2,
			LeverFacing::SOUTH()->id() => 3,
			LeverFacing::NORTH()->id() => 4,
			LeverFacing::UP_AXIS_Z()->id() => 5,
			LeverFacing::UP_AXIS_X()->id() => 6,
			LeverFacing::DOWN_AXIS_Z()->id() => 7,
			default => throw new AssumptionFailedError(),
		};
		return $rotationMeta | ($this->activated ? BlockLegacyMetadata::LEVER_FLAG_POWERED : 0);
	}

	public function readStateFromData(int $id, int $stateMeta) : void{
		$rotationMeta = $stateMeta & 0x07;
		$this->facing = match($rotationMeta){
			0 => LeverFacing::DOWN_AXIS_X(),
			1 => LeverFacing::EAST(),
			2 => LeverFacing::WEST(),
			3 => LeverFacing::SOUTH(),
			4 => LeverFacing::NORTH(),
			5 => LeverFacing::UP_AXIS_Z(),
			6 => LeverFacing::UP_AXIS_X(),
			7 => LeverFacing::DOWN_AXIS_Z(),
			default => throw new AssumptionFailedError("0x07 mask should make this impossible"), //phpstan doesn't understand :(
		};

		$this->activated = ($stateMeta & BlockLegacyMetadata::LEVER_FLAG_POWERED) !== 0;
	}

	public function getFacing() : LeverFacing{ return $this->facing; }

	/** @return $this */
	public function setFacing(LeverFacing $facing) : self{
		$this->facing = $facing;
		return $this;
	}

	public function isActivated() : bool{ return $this->activated; }

	/** @return $this */
	public function setActivated(bool $activated) : self{
		$this->activated = $activated;
		return $this;
	}

	public function getStateBitmask() : int{
		return 0b1111;
	}

	public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if(!$blockClicked->isSolid()){
			return false;
		}

		$selectUpDownPos = function(LeverFacing $x, LeverFacing $z) use ($player) : LeverFacing{
			if($player !== null){
				return Facing::axis($player->getHorizontalFacing()) === Axis::X ? $x : $z;
			}
			return $x;
		};
		$this->facing = match($face){
			Facing::DOWN => $selectUpDownPos(LeverFacing::DOWN_AXIS_X(), LeverFacing::DOWN_AXIS_Z()),
			Facing::UP => $selectUpDownPos(LeverFacing::UP_AXIS_X(), LeverFacing::UP_AXIS_Z()),
			Facing::NORTH => LeverFacing::NORTH(),
			Facing::SOUTH => LeverFacing::SOUTH(),
			Facing::WEST => LeverFacing::WEST(),
			Facing::EAST => LeverFacing::EAST(),
			default => throw new AssumptionFailedError("Bad facing value"),
		};

		return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
	}

	public function onNearbyBlockChange() : void{
		if(!$this->getSide(Facing::opposite($this->facing->getFacing()))->isSolid()){
			if($this->isActivated()){
				foreach($this->getSidesHCF(2) as $block){
					if($block instanceof Door) {
						$block->setOpen(!$block->isOpen());
					}
				}
			}
			$this->position->getWorld()->useBreakOn($this->position);
		}
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		$meta = $this->activated;
		$this->activated = !$this->activated;
		$this->position->getWorld()->setBlock($this->position, $this);
		$this->position->getWorld()->addSound(
			$this->position->add(0.5, 0.5, 0.5),
			$this->activated ? new RedstonePowerOnSound() : new RedstonePowerOffSound()
		);
		if($this->isActivated() !== $meta){
			foreach($this->getSidesHCF(2) as $block){
				if($block instanceof Door) {
					$block->setOpen(!$block->isOpen());
				}
			}
		}
		return true;
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
