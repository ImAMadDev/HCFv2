<?php

namespace ImAMadDev\entity\projectile;

use ImAMadDev\{Loader, Factions};
use ImAMadDev\player\Player;

use ImAMadDev\item\specials\EggPorts;

use ImAMadDev\API\projectile\Throwable;

use pocketmine\utils\TextFormat as TE;
use pocketmine\math\Vector3;
use pocketmine\level\Level;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;

use pocketmine\math\RayTraceResult;

use pocketmine\event\entity\{ProjectileHitEvent, ProjectileHitEntityEvent};

class Egg extends Throwable {

    const NETWORK_ID = self::EGG;

    /** @var float */
    public $width = 0.5, $length = 0.5, $height = 0.5;

    /** @var float */
    protected $gravity = 0.03, $drag = 0.01;

    /**
     * Egg Constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     * @param Entity $shootingEntity
     */
    public function __construct(Level $level, CompoundTag $nbt, ?Entity $shootingEntity = null){
        parent::__construct($level, $nbt, $shootingEntity);
    }

    /**
     * @param ProjectileHitEvent $event
     * @return void
     */
    public function onHit(ProjectileHitEvent $event) : void {
        $sender = $this->getOwningEntity();
        if($sender instanceof Player && $event instanceof ProjectileHitEntityEvent){
            $item = $sender->getInventory()->getItemInHand();
            if(!Factions::isSpawnRegion($sender) && $item instanceof EggPorts && $item->getNamedTagEntry(EggPorts::CUSTOM_ITEM) instanceof CompoundTag){
                $player = $event->getEntityHit();
                if($player instanceof Player){
                    $position = $player->getPosition();
                    $player->teleport($sender->getPosition());
                    $sender->teleport($position);
                }
            }
        }
    }
}

?>