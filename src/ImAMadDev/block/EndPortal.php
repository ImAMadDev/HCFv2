<?php

namespace ImAMadDev\block;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\Server;
use pocketmine\block\Transparent;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;

class EndPortal extends Transparent {

    public function __construct() {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::END_PORTAL, 0), "End Portal", BlockBreakInfo::indestructible());
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "End Portal";
    }

    /**
     * @return bool
     */
    public function hasEntityCollision(): bool {
        return true;
    }

    /**
     * @param Item $item
     * @return array
     */
    public function getDrops(Item $item): array {
        return [];
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function onEntityInside(Entity $entity): bool {
        if(!$entity instanceof HCFPlayer) {
            return false;
        }
        if($entity->endQueue) {
        	return false;
        }
        if($entity->getCooldown()->has('combattag')) {
            $entity->sendBack($this->getPosition()->asVector3(), 1);
            return false;
        }
        $entity->endQueue = true;
        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new class($entity) extends Task {
            private HCFPlayer $player;
            public function __construct(HCFPlayer $player) {
                $this->player = $player;
            }
            public function onRun() : void {
                if($this->player->isOnline() === false) {
                    return;
                }
                if($this->player->getWorld()->getBlock($this->player->getPosition())->getId() !== BlockLegacyIds::END_PORTAL) {
                    $this->player->endQueue = false;
                    return;
                }
                $position = Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::END_MAP)->getSpawnLocation();
                if($this->player->getWorld()->getFolderName() === HCFUtils::END_MAP) {
                    $position = Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::DEFAULT_MAP)->getSpawnLocation();
                }
                $this->player->teleport($position);
                $this->player->endQueue = false;
            }
        }, 60);
        return true;
    }
}