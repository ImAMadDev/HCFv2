<?php

namespace ImAMadDev\item;

use pocketmine\item\ItemUseResult;
use pocketmine\item\PotionType;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ProjectileItem;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class SplashPotion extends ProjectileItem {

    private PotionType $potionType;

    /**
     * SplashPotion Constructor.
     * @param ItemIdentifier $identifier
     * @param string $name
     * @param PotionType $potionType
     */
	public function __construct(ItemIdentifier $identifier, string $name, PotionType $potionType){
		parent::__construct($identifier, $name);
        $this->potionType = $potionType;
	}

    /**
     * @param Player $player
     * @param Vector3 $directionVector
     * @return ItemUseResult
     */
	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
		return ItemUseResult::SUCCESS();
	}
	
	/**
	 * @return Int
	 */
	public function getMaxStackSize() : int {
		return 1;
	}
	
	/**
	 * @return float
	 */
	public function getThrowForce() : float {
        return 0.7;
	}

    /**
     * @return PotionType
     */
    public function getPotionType(): PotionType
    {
        return $this->potionType;
    }

    /**
     * @param PotionType $potionType
     */
    public function setPotionType(PotionType $potionType): void
    {
        $this->potionType = $potionType;
    }

    protected function createEntity(Location $location, Player $thrower): Throwable
    {
        return new \ImAMadDev\entity\projectile\SplashPotion($location, $thrower, $this->getPotionType());
    }
}