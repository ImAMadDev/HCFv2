<?php
namespace ImAMadDev\entity\mobs;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\world\Explosion;

class Creeper extends Living {

    /** @var float */
    public float $height = 1.5;

    /** @var float */
    public float $width = 0.9;

    /** @var int */
    protected int $blowingTimer = 0;

    /**
     * @return string
     */
    public function getName(): string {
        return "Creeper";
    }

    public function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);

        $this->setMaxHealth(20);
        $this->setHealth(20);
    }

    /**
     * @param int $tickDiff
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool {
        if($this->blowingTimer > 0){
            if(--$this->blowingTimer == 0){
                $exp = new Explosion($this->getPosition(), 2);
                $exp->explodeB();
                $this->kill();
            }
        }
        return parent::entityBaseTick($tickDiff);
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void {
        parent::attack($source);
        if($this->blowingTimer == 0){
            $this->blowingTimer = 45;
            $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::IGNITED, true);
        }
    }

    /**
     * @return array
     */
    public function getDrops(): array {
        return [ItemFactory::getInstance()->get(ItemIds::GUNPOWDER, 0, mt_rand(0, 5))];
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
        return EntityIds::CREEPER;
    }
}