<?php

namespace ImAMadDev\entity\mobs;

use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\block\{Block,BlockFactory,FenceGate,Fence,Liquid,Stair,Slab};
use JetBrains\PhpStorm\Pure;
use pocketmine\math\{Math,Vector2,Vector3,VoxelRayTrace};
use pocketmine\entity\Living;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\world\World;
use pocketmine\player\GameMode;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\player\Player;

use ImAMadDev\faction\Faction;

class Protector extends Living {

    public static function getNetworkTypeId() : string{ return EntityIds::SILVERFISH; }
    public const TARGET_MAX_DISTANCE = 30;

    #[Pure] protected function getInitialSizeInfo() : EntitySizeInfo{
        return new EntitySizeInfo(0.5, 0.5); //TODO: eye height ??
    }

    public $target;
    
    public ?string $faction = null;
    public $timer = 0;
    public $deadtime = 60;
    
    public $speed = 0.4;
    
    public $attackDelay = 0;
    
    public $stayTime = 0;
    
    public $moveTime = 0;
    
    public function __construct(World $world, CompoundTag $nbt){
        parent::__construct($world, $nbt);
    }
    
    public function initEntity(CompoundTag $nbt): void{
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        $this->setHealth(20);
        $this->setMaxHealth(20);
        parent::initEntity($nbt);
    }

    public function getName(): string{
        return "Protector";
    }

    public function entityBaseTick(int $tickDiff = 1): bool{
        if(!$this->isAlive() || $this->isClosed()){
            return false;
        }
        if($this->faction == null){
            $this->flagForDespawn();
            return false;
        }
        if(!$this->level instanceof World){
            $this->flagForDespawn();
            return false;
        }
        parent::entityBaseTick($tickDiff);
        
        $this->timer++;
        if($this->timer >= 20){
            $this->deadtime--;
            $this->timer = 0;
        }
        if($this->deadtime <= 0){
         
            $this->kill();
            return false;

        }
        $this->updateNametag();
        
        $this->updateMove($tickDiff);

        if($this->target instanceof Player){
            $this->checkEntity($this->target);
        }
        if($this->target instanceof Player){
            $this->attackEntity($this->target);
        }elseif(
            $this->target instanceof Vector3
            && $this->distanceSquared($this->target) <= 1
            && $this->motion->y == 0
        ){
            $this->moveTime = 0;
        }

        return true;
    }
    
    public function checkEntity(Living $player): void{
        if($player instanceof Player){
            if($player->getFaction()?->getName() == $this->faction){
                $this->target = null;
            } 
            if($player->getGamemode() !== GameMode::SURVIVAL() && $player->getGamemode() !== GameMode::ADVENTURE()){
                $this->target = null;
            }
            if($this->distance($player) > self::TARGET_MAX_DISTANCE){
                $this->target = null;
            }
        }
    }

    public function attackEntity(Living $player): void{
        if($this->attackDelay > 16 && $this->boundingBox->intersectsWith($player->getBoundingBox(), -1)){
        	if($player instanceof Player) {
				$damage = 1;
				$ev = new EntityDamageByEntityEvent($this->owner, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $damage);
				$player->attack($ev);
				$this->broadcastEntityEvent(ActorEvent::ARM_SWING);

				$this->attackDelay = 0;
			}
			$this->attackDelay++;
        }

    }

    public function updateMove($tickDiff){
        if($this->world === null){
            return null;
        }
        $before = $this->target;
        $this->changeTarget();
        if($this->target instanceof Player || $this->target instanceof Block || $before !== $this->target && $this->target !== null){
            $x = $this->target->x - $this->x;
            $y = $this->target->y - ($this->y + $this->eyeHeight);
            $z = $this->target->z - $this->z;


            $diff = abs($x) + abs($z);
            if($x ** 2 + $z ** 2 < 0.7){
                $this->motion->x = 0;
                $this->motion->z = 0;
            }elseif($diff > 0){
                $this->motion->x = $this->speed * 0.15 * ($x / $diff);
                $this->motion->z = $this->speed * 0.15 * ($z / $diff);
                $this->yaw = -atan2($x / $diff, $z / $diff) * 180 / M_PI;
            }
            $this->pitch = $y == 0 ? 0 : rad2deg(-atan2($y, sqrt($x ** 2 + $z ** 2)));
        }

        $dx = $this->motion->x * $tickDiff;
        $dz = $this->motion->z * $tickDiff;
        $isJump = false;
        $this->checkBlockCollision();

        $bb = $this->boundingBox;

        $minX = (int) floor($bb->minX - 0.5);
        $minY = (int) floor($bb->minY);
        $minZ = (int) floor($bb->minZ - 0.5);
        $maxX = (int) floor($bb->maxX + 0.5);
        $maxY = (int) floor($bb->maxY);
        $maxZ = (int) floor($bb->maxZ + 0.5);

        for($z = $minZ; $z <= $maxZ; ++$z){
            for($x = $minX; $x <= $maxX; ++$x){
                for($y = $minY; $y <= $maxY; ++$y){
                    $block = $this->level->getBlockAt($x, $y, $z);
                    if(!$block->hasEntityCollision()){
                        foreach($block->getCollisionBoxes() as $blockBB){
                            if($blockBB->intersectsWith($bb, -0.01)){
                                $this->isCollidedHorizontally = true;
                            }
                        }
                    }
                }
            }
        }

        if($this->isCollidedHorizontally or $this->isUnderwater()){
            $isJump = $this->checkJump($dx, $dz);
            $this->updateMovement();
        }
        if($this->stayTime > 0){
            $this->stayTime -= $tickDiff;
            $this->move(0, $this->motion->y * $tickDiff, 0);
        }else{
            $futureLocation = new Vector2($this->x + $dx, $this->z + $dz);
            $this->move($dx, $this->motion->y * $tickDiff, $dz);
            $myLocation = new Vector2($this->x, $this->z);
            if(($futureLocation->x != $myLocation->x || $futureLocation->y != $myLocation->y) && !$isJump){
                $this->moveTime -= 90 * $tickDiff;
            }
        }

        if(!$isJump){
            if($this->isOnGround()){
                $this->motion->y = 0;
            }elseif($this->motion->y > -$this->gravity * 4){
                if(!($this->getWorld()->getBlock(new Vector3(Math::floorFloat($this->x), (int) ($this->y + 0.8), Math::floorFloat($this->z))) instanceof Liquid)){
                    $this->motion->y -= $this->gravity;
                }
            }else{
                $this->motion->y -= $this->gravity * $tickDiff;
            }
        }
        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
        $this->updateMovement();

        parent::updateMovement();

        return $this->target;
    }

    private function checkJump($dx, $dz): bool{
        if($this->motion->y == $this->gravity * 2){
            return $this->getWorld()->getBlock(new Vector3(Math::floorFloat($this->x), (int) $this->y, Math::floorFloat($this->z))) instanceof Liquid;
        }else{
            if($this->getWorld()->getBlock(new Vector3(Math::floorFloat($this->x), (int) ($this->y + 0.8), Math::floorFloat($this->z))) instanceof Liquid){
                $this->motion->y = $this->gravity * 2;
                return true;
            }
        }
        if($this->motion->y > 0.1 or $this->stayTime > 0){
            return false;
        }
        if($this->getDirection() === null){
            return false;
        }

        $blockingBlock = $this->getWorld()->getBlock($this);
        if($blockingBlock->hasEntityCollision()){
            try{
                $blockingBlock = $this->getTargetBlock(2);
            }catch(InvalidStateException $ex){
                return false;
            }
        }
        if($blockingBlock != null and !$blockingBlock->hasEntityCollision()){
            $upperBlock = $this->getWorld()->getBlock($blockingBlock->add(0, 1, 0));
            $secondUpperBlock = $this->getWorld()->getBlock($blockingBlock->add(0, 2, 0));

            if($upperBlock->hasEntityCollision() && $secondUpperBlock->hasEntityCollision()){
                if($blockingBlock instanceof Fence || $blockingBlock instanceof FenceGate){
                    $this->motion->y = $this->gravity;
                }else if($blockingBlock instanceof Slab or $blockingBlock instanceof Stair){
                    $this->motion->y = $this->gravity * 5;
                     $this->setMotion($this->getDirectionVector()->add(0, 0)->multiply(0.9));
                }else if($this->motion->y < ($this->gravity * 3.2)){ // Magic
                    $this->motion->y = $this->gravity * 3.2;
                }else{
                    $this->motion->y += $this->gravity * 0.29;
                }
                return true;
            }elseif(!$upperBlock->hasEntityCollision()()){
                $this->yaw = $this->getYaw() + mt_rand(-120, 120) / 10;
            }
        }
        return false;
    }

    private function updateNametag(): void{
        $bar = "§6Protector §r§b" . $this->deadtime . "s";
        $tag = "\n§r§f{$this->getHealth()}";
        $this->setNameTag($bar . "" . $tag);
    }

    private function changeTarget(): void{
        if($this->target instanceof Player and $this->target->isAlive()){
            return;
        }
        if(!$this->target instanceof Player || !$this->target->isAlive() || $this->target->isClosed()){
            foreach($this->getWorld()->getEntities() as $entity){
                if($entity === $this || !($entity instanceof Player) || $entity instanceof self){
                    continue;
                }
                if($this->distanceSquared($entity) > self::TARGET_MAX_DISTANCE){
                    continue;
                }
                if($entity instanceof Player){
                    if($entity->getGamemode() !== GameMode::ADVENTURE() && $entity->getGamemode() !== GameMode::SURVIVAL()){
                        continue;
                    }
                    if($entity->getFaction()?->getName() == $this->faction){
                        continue;
                    }
                }
                $this->target = $entity;
            }
        }
    }

    public function onMove(PlayerMoveEvent $event){

    }

    public function getTargetBlock(int $maxDistance, array $transparent = []): ?Block{
        $line = $this->getLineOfSight($maxDistance, 1, $transparent);
        if(!empty($line)){
            return array_shift($line);
        }

        return null;
    }

    /**
     * @param int   $maxDistance
     * @param int   $maxLength
     * @param array $transparent
     *
     * @return Block[]
     */
    public function getLineOfSight(int $maxDistance, int $maxLength = 0, array $transparent = []) : array{
        if($maxDistance > 120){
            $maxDistance = 120;
        }

        if(count($transparent) === 0){
            $transparent = null;
        }

        $blocks = [];
        $nextIndex = 0;

        foreach(VoxelRayTrace::inDirection($this, $this->getDirectionVector(), $maxDistance) as $vector3){
            $block = $this->world->getBlockAt($vector3->x, $vector3->y, $vector3->z);
            $blocks[$nextIndex++] = $block;

            if($maxLength !== 0 and count($blocks) > $maxLength){
                array_shift($blocks);
                --$nextIndex;
            }

            $id = $block->getId();

            if($transparent === null){
                if($id !== 0){
                    break;
                }
            }else{
                if(!isset($transparent[$id])){
                    break;
                }
            }
        }

        return $blocks;
    }

    public function attack(EntityDamageEvent $source): void{
        if($this->noDamageTicks > 0){
            $source->cancel();
        }elseif($this->attackTime > 0){
            $lastCause = $this->getLastDamageCause();
            if($lastCause !== null and $lastCause->getBaseDamage() >= $source->getBaseDamage()){
                $source->cancel();
            }
        }
        if($source instanceof EntityDamageByEntityEvent){
            if($source->getDamager() instanceof Player){
            	if($source->getDamager()->getFaction()?->getName() == $this->faction){
                	$source->cancel();
            	}
            }
            if($source->getDamager()->distance($this->owner) < 1){
                $this->setMotion($this->getDirectionVector()->add(0, 0)->multiply(0.9));
            }
            $source->setKnockback(0.1);
        }
        parent::attack($source);
    }
    
    public function getXpDropAmount(): int {
        return 0;
    }
}