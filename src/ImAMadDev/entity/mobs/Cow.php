<?php

namespace ImAMadDev\entity\mobs;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Ageable;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Cow extends Living implements Ageable {

	/** @var float */
	public float $height = 1.5;

    public float $width = 0.9;

    /**
	 * @return String
	 */
	public function getName() : string {
		return "Cow";
	}
	
	/**
	 * @return array
	 */
	public function getDrops() : array {
		return [
			ItemFactory::getInstance()->get(ItemIds::STEAK, 0, mt_rand(1, 3)),
            ItemFactory::getInstance()->get(ItemIds::LEATHER, 0, mt_rand(0, 2)),
        ];
    }
    
    /**
     * @return Int
     */
    public function getXpDropAmount() : int {
    	return 0;
    }

    public function isBaby(): bool
    {
        return false;
    }

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::COW;
    }
}