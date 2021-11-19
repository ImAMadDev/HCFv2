<?php
namespace ImAMadDev\entity\mobs;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class Blaze extends Living {


    /** @var float */
    public float $height = 2.0;

    /** @var float */
    public float $width = 1.0;
    
    /**
     * @return string
     */
    public function getName(): string {
        return "Blaze";
    }

    public function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);

        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    /**
     * @return array
     */
    public function getDrops(): array {
        return [ItemFactory::getInstance()->get(ItemIds::BLAZE_ROD, 0, mt_rand(0, 1))];
    }
    
    /**
     * @return Int
     */
    public function getXpDropAmount() : int {
    	return 0;
    }

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo { return new EntitySizeInfo($this->height, $this->width); }

    public static function getNetworkTypeId(): string { return EntityIds::BLAZE; }
}