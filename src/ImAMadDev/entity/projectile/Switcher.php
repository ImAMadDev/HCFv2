<?php

namespace ImAMadDev\entity\projectile;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\entity\projectile\Throwable;
use ImAMadDev\manager\ClaimManager;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;

use pocketmine\event\entity\{ProjectileHitEvent, ProjectileHitEntityEvent};

class Switcher extends Throwable {

	public float $length = 0.5;
    public int|float $width = 0.5;
    public int|float $height = 0.5;


    public function __construct(Location $location, ?Entity $entity, ?CompoundTag $nbt = null){
		parent::__construct($location, $entity, $nbt);
	}
	
	public function onHit(ProjectileHitEvent $event) : void {
		$sender = $this->getOwningEntity();
		if($sender instanceof HCFPlayer && $event instanceof ProjectileHitEntityEvent){
			$claim = ClaimManager::getInstance()->getClaimNameByPosition($sender->getPosition());
			if(stripos($claim, "Spawn") === false){
				$player = $event->getEntityHit();
				if($player instanceof HCFPlayer){
					$claim2 = ClaimManager::getInstance()->getClaimNameByPosition($sender->getPosition());
					if(stripos($claim2, "Spawn") === false && round($sender->getPosition()->distance($player->getPosition())) <= 20){
						$position = $player->getPosition();
						$player->teleport($sender->getPosition());
						$sender->teleport($position);
					}
                }
            }
        }
    }

    #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::SNOWBALL;
    }
}