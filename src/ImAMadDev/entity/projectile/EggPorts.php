<?php

namespace ImAMadDev\entity\projectile;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\entity\projectile\Throwable;
use ImAMadDev\ability\Ability;

use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Location;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

use pocketmine\event\entity\{ProjectileHitEvent, ProjectileHitEntityEvent};

class EggPorts extends Throwable {
	
	public float $length = 0.5;
	public int|float $width = 0.5;
	public int|float $height = 0.5;
	
	public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null){
		parent::__construct($location, $shootingEntity, $nbt);
	}
	
	public function onHit(ProjectileHitEvent $event): void 
	{
		$sender = $this->getOwningEntity();
		if($sender instanceof HCFPlayer && $event instanceof ProjectileHitEntityEvent){
			$claim = ClaimManager::getInstance()->getClaimNameByPosition($sender->getPosition());
			if(stripos($claim, "Spawn") === false){
				$player = $event->getEntityHit();
				if($player instanceof HCFPlayer){
					$claim2 = ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition());
					if(stripos($claim2, "Spawn") === false && round($sender->getPosition()->distance($player->getPosition())) <= 30){
						$position = clone $player->getPosition();
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
        return EntityIds::EGG;
    }
}

?>