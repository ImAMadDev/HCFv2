<?php

namespace ImAMadDev\block;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\block\Transparent;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\utils\TextFormat;
use pocketmine\world\utils\SubChunkExplorer;
use pocketmine\world\utils\SubChunkExplorerStatus;
use pocketmine\world\World;
use SplQueue;

class Portal extends Transparent {

    public function __construct() {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::PORTAL, 0), "Nether Portal", BlockBreakInfo::indestructible());
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
                if ($this->player->getCooldown()->has("combattag")) {
                    $this->player->portalQueue = false;
                    return;
                }
                $position = Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::NETHER_MAP)->getSpawnLocation();
                if($this->player->getWorld()->getFolderName() == HCFUtils::NETHER_MAP) {
                    $position = Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::DEFAULT_MAP)->getSpawnLocation();
                }
                $this->player->teleport($position);
                $this->player->portalQueue = false;
            }
        }, 80);
        return true;
    }

    public function onNearbyBlockChange(): void
    {
        $pos = $this->getPosition();
        $world = $pos->getWorld();

        $shouldKeep = 1;
        if($pos->y < World::Y_MAX - 1){
            $shouldKeep &= $this->isValid($world->getBlockAt($pos->x, $pos->y + 1, $pos->z));
        }
        if($pos->y > 0){
            $shouldKeep &= $this->isValid($world->getBlockAt($pos->x, $pos->y - 1, $pos->z));
        }

        $metadata = $this->getMeta();
        if($metadata < 2){
            $shouldKeep &= $this->isValid($world->getBlockAt($pos->x - 1, $pos->y, $pos->z));
            $shouldKeep &= $this->isValid($world->getBlockAt($pos->x + 1, $pos->y, $pos->z));
        }else{
            $shouldKeep &= $this->isValid($world->getBlockAt($pos->x, $pos->y, $pos->z - 1));
            $shouldKeep &= $this->isValid($world->getBlockAt($pos->x, $pos->y, $pos->z + 1));
        }

        if($shouldKeep === 0){
            $this->fill($world, $pos, $metadata);
            return;
        }

        parent::onNearbyBlockChange(); // TODO: Change the autogenerated stub
    }

    public function isValid(Block $block) : bool{
        $blockId = $block->getId();
        return $blockId === BlockLegacyIds::PORTAL;
    }

    public function fill(World $world, Vector3 $origin, int $metadata) : void{
        $visits = new SplQueue();
        $visits->enqueue($origin);

        $iterator = new SubChunkExplorer($world);
        $air = VanillaBlocks::AIR();

        $block_factory = BlockFactory::getInstance();

        while(!$visits->isEmpty()){
            /** @var Vector3 $coordinates */
            $coordinates = $visits->dequeue();
            if(
                $iterator->moveTo($coordinates->x, $coordinates->y, $coordinates->z) === SubChunkExplorerStatus::INVALID ||
                $block_factory->fromFullBlock($iterator->currentSubChunk->getFullBlock($coordinates->x & 0x0f, $coordinates->y & 0x0f, $coordinates->z & 0x0f))->getId() !== BlockLegacyIds::PORTAL
            ){
                continue;
            }

            $world->setBlockAt($coordinates->x, $coordinates->y, $coordinates->z, $air);

            if($metadata === 0){
                $visits->enqueue($coordinates->getSide(Facing::EAST));
                $visits->enqueue($coordinates->getSide(Facing::WEST));
            }else{
                $visits->enqueue($coordinates->getSide(Facing::NORTH));
                $visits->enqueue($coordinates->getSide(Facing::SOUTH));
            }

            $visits->enqueue($coordinates->getSide(Facing::UP));
            $visits->enqueue($coordinates->getSide(Facing::DOWN));
        }
    }
}