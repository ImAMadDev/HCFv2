<?php

namespace ImAMadDev\entity\mobs;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Enderman extends Living {

	/** @var float */
	public float $height = 3.5;
    public float $width = 0.6;

    /**
	 * @return String
	 */
	public function getName() : string {
		return "Enderman";
	}
	
	/**
	 * @return array
	 */
	public function getDrops() : array {
		return [
			ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, mt_rand(1, 2)),
        ];
    }
    
    /**
     * @return Int
     */
    public function getXpDropAmount() : int {
    	return 0;
    }

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::ENDERMAN;
    }
}
