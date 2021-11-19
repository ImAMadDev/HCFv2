<?php

namespace ImAMadDev\block;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\ItemIds;
use pocketmine\Server;
use pocketmine\block\Transparent;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;

class Portal extends Transparent {

    public function __construct() {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::PORTAL, 0, ItemIds::PORTAL), "Nether Portal", BlockBreakInfo::indestructible(0.0));
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Nether Portal";
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
        if($entity->portalQueue) {
        	return false;
        }
        if($entity->getCooldown()->has('combattag')) {
            $entity->sendBack($this->getPosition()->asVector3(), 1);
            return false;
        }
        $entity->portalQueue = true;
        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new class($entity) extends Task {
            private HCFPlayer $player;
            public function __construct(HCFPlayer $player) {
                $this->player = $player;
            }
            public function onRun() : void {
                if($this->player->isOnline() === false) {
                    return;
                }
                if($this->player->getWorld()->getBlock($this->player->getPosition())->getId() !== BlockLegacyIds::PORTAL) {
                    $this->player->portalQueue = false;
                    return;
                }
                $dimension = DimensionIds::NETHER;
                if($this->player->getWorld()->getFolderName() === HCFUtils::NETHER_MAP) {
                    $dimension = DimensionIds::OVERWORLD;
                }
                $position = Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::NETHER_MAP)->getSpawnLocation();
                if($this->player->getWorld()->getFolderName() === HCFUtils::NETHER_MAP) {
                    $position = Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::DEFAULT_MAP)->getSpawnLocation();
                }
                $this->player->sendDimension($dimension);
                $this->player->getNetworkSession()->sendDataPacket(PlayStatusPacket::create(PlayStatusPacket::PLAYER_SPAWN));
                $this->player->teleport($position);
                $this->player->portalQueue = false;
            }
        }, 80);
        return true;
    }
}