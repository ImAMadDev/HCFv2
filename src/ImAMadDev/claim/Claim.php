<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\Air;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\world\{World, Position};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\koth\KOTHArena;
use ImAMadDev\manager\EOTWManager;
use ImAMadDev\faction\Faction;

class Claim {
	
	public HCF $main;

    private ClaimType $claimType;

    private ClaimProperties $properties;
	
	public function __construct(HCF $main, array $data) {
		$this->main = $main;
        $this->claimType = new ClaimType($data['claim_type'] ?? ClaimType::FACTION);
        $this->properties = new ClaimProperties($data);
        var_dump($this->getSize());
	}
	
	public function getWorldName() : string {
		return $this->getProperties()->getWorld()->getFolderName();
	}
	
	public function getSize(): int 
	{
		/*$firstX = $this->getProperties()->getPosition1()->getX();
		$secondX = $this->getProperties()->getPosition2()->getX();
		$firstZ = $this->getProperties()->getPosition1()->getZ();
		$secondZ = $this->getProperties()->getPosition2()->getZ();
		$length = max($firstX, $secondX) - min($firstX, $secondX);
		$width = max($firstZ, $secondZ) - min($firstZ, $secondZ);
		return $length * $width;*/
		$first = $this->getProperties()->getPosition1();
		$second = $this->getProperties()->getPosition2();
		return sqrt((($first->x - $second->x) ** 2)  + (($first->z - $second->z) ** 2));
	}
	
	public function isInside(Position $position): bool {
		$world = $position->getWorld();
		$minX = min($this->getProperties()->getPosition1()->getX(), $this->getProperties()->getPosition2()->getX());
		$maxX = max($this->getProperties()->getPosition1()->getX(), $this->getProperties()->getPosition2()->getX());
		$minY = 0;
		$maxY = World::Y_MAX;
		$minZ = min($this->getProperties()->getPosition1()->getZ(), $this->getProperties()->getPosition2()->getZ());
		$maxZ = max($this->getProperties()->getPosition1()->getZ(), $this->getProperties()->getPosition2()->getZ());
		return $minX <= $position->getX() and $maxX >= $position->getFloorX() and $minY <= $position->getY() and
			$maxY >= $position->getY() and $minZ <= $position->getZ() and $maxZ >= $position->getFloorZ() and
            $world->getFolderName() === $this->getWorldName();
	}
	
	#[Pure] public function intersectsWith(Position $firstPosition, Position $secondPosition, float $epsilon = 0.00001) : bool{
		$minX = min($this->getProperties()->getPosition1()->getFloorX(), $this->getProperties()->getPosition2()->getFloorX());
		$maxX = max($this->getProperties()->getPosition1()->getFloorX(), $this->getProperties()->getPosition2()->getFloorX());
		$minY = 0;
		$maxY = World::Y_MAX;
		$minZ = min($this->getProperties()->getPosition1()->getFloorZ(), $this->getProperties()->getPosition2()->getFloorZ());
		$maxZ = max($this->getProperties()->getPosition1()->getFloorZ(), $this->getProperties()->getPosition2()->getFloorZ());
		if(max($firstPosition->getFloorX(), $secondPosition->getFloorX()) - $minX > $epsilon and $maxX - min($firstPosition->getFloorX(), $secondPosition->getFloorX()) > $epsilon){
			if(max($firstPosition->getFloorY(), $secondPosition->getFloorY()) - $minY > $epsilon and $maxY - min($firstPosition->getFloorY(), $secondPosition->getFloorY()) > $epsilon){
				return max($firstPosition->getFloorZ(), $secondPosition->getFloorZ()) - $minZ > $epsilon and $maxZ - min($firstPosition->getFloorZ(), $secondPosition->getFloorZ()) > $epsilon;
			}
		}
		return false;
	}
	/*
	protected function getClaimsIntersected(Position $pos1, Position $pos2, float $inset) : \Generator{
		$minX = (int) floor($pos1->minX + $inset);
		$minZ = (int) floor($pos1->minZ + $inset);
		$maxX = (int) floor($os2->maxX - $inset);
		$maxZ = (int) floor($pos2->maxZ - $inset);

		$world = $pos->getWorld();
		
		for($z = $minZ; $z <= $maxZ; ++$z){
			for($x = $minX; $x <= $maxX; ++$x){
					yield ClaimManager::getInstance()->getClaimAt($x, $y, $z);
				}
			}
		}
	}
	
	protected function getBlocksAroundWithEntityInsideActions() : array{
		if($this->blocksAround === null){
			$this->blocksAround = [];

			$inset = 0.001; //Offset against floating-point errors
			foreach($this->getBlocksIntersected($inset) as $block){
				if($block->hasEntityCollision()){
					$this->blocksAround[] = $block;
				}
			}
		}

		return $this->blocksAround;
	}*/
	
	public function getFaction() : ? Faction {
		return (HCF::getFactionManager()->getFaction($this->getProperties()->getName()));
	}
	
	public function canEdit(?Faction $faction) : bool {
		if($this->getClaimType()->getType() == ClaimType::FACTION) {
			$faction_claim = HCF::getFactionManager()->getFaction($this->getProperties()->getName());
			if($faction_claim->getDTR() <= 0) {
				return true;
			}
			if($faction instanceof Faction) {
				return ($faction_claim->getName() == $faction->getName());
			} else {
				return false;
			}
		}
        return $this->getProperties()->isEditable();
	}
	
	public function join(HCFPlayer $player) : bool {
		$faction = $this->getFaction();
        if ($player->getGamemode() === GameMode::CREATIVE()) return true;
		if($faction instanceof Faction) {
			if($player->isInvincible()) return false;
			if($faction->getDTR() <= 0) {
				if($player->getGamemode() === GameMode::ADVENTURE()) {
					$player->setGamemode(GameMode::SURVIVAL());
                    //$this->setEdit($player, true);
				}
            } else {
				if($faction->isInFaction($player->getName())) {
					if($player->getGamemode() === GameMode::ADVENTURE()) {
						$player->setGamemode(GameMode::SURVIVAL());
                        //$this->setEdit($player, true);
					}
                } else {
					if($player->getGamemode() === GameMode::SURVIVAL()) {
						$player->setGamemode(GameMode::ADVENTURE());
                        //$this->setEdit($player);
					}
                }
            }
        } else {
			if($this->getClaimType()->getType() === ClaimType::SPAWN) {
				if($player->getCooldown()->has('combattag') && EOTWManager::isEnabled() === false) return false;
            } else {
				if($player->getGamemode() === GameMode::ADVENTURE()) {
					$player->setGamemode(GameMode::SURVIVAL());
                    //$this->setEdit($player, true);
				}
            }
        }
        return true;
    }

    #[Pure] public function getSafety() : string
    {
        if ($this->getClaimType()->getType() === ClaimType::SPAWN) return TextFormat::GREEN . '(Non-DeathBan)';
        return TextFormat::RED . '(DeathBan)';
    }
	
	public function viewMap(HCFPlayer $player){
        $blocks = [BlockFactory::getInstance()->get(BlockLegacyIds::GLASS, 0), BlockFactory::getInstance()->get(BlockLegacyIds::DIAMOND_BLOCK, 0)];
		$position1 = new Vector3($this->getProperties()->getPosition1()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition1()->getFloorZ());
		$position2 = new Vector3($this->getProperties()->getPosition2()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition2()->getFloorZ());
		$position3 = new Vector3($this->getProperties()->getPosition1()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition2()->getFloorZ());
		$position4 = new Vector3($this->getProperties()->getPosition2()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition1()->getFloorZ());
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
			if($player->getPosition()->getWorld()->getBlock(new Vector3($position1->getFloorX(), $i, $position1->getFloorZ())) instanceof Air) {
            	$pos = new BlockPosition($position1->getFloorX(), $i, $position1->getFloorZ());
            	$block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            	$pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
				$player->getNetworkSession()->sendDataPacket($pk);
			}
		}
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
			if($player->getPosition()->getWorld()->getBlock(new Vector3($position1->getFloorX(), $i, $position1->getFloorZ())) instanceof Air) {
        	    $pos = new BlockPosition($position2->getFloorX(), $i, $position2->getFloorZ());
          	  $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            	$pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
           	 $player->getNetworkSession()->sendDataPacket($pk);
           }
		}
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
			if($player->getPosition()->getWorld()->getBlock(new Vector3($position1->getFloorX(), $i, $position1->getFloorZ())) instanceof Air) {
           	 $pos = new BlockPosition($position3->getFloorX(), $i, $position3->getFloorZ());
         	   $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
           	 $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
           	 $player->getNetworkSession()->sendDataPacket($pk);
           }
		}
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
			if($player->getPosition()->getWorld()->getBlock(new Vector3($position1->getFloorX(), $i, $position1->getFloorZ())) instanceof Air) {
        	    $pos = new BlockPosition($position4->getFloorX(), $i, $position4->getFloorZ());
           	 $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            	$pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            	$player->getNetworkSession()->sendDataPacket($pk);
            }
		}
	}

    public function cancelMap(HCFPlayer $player){
        $position1 = new Vector3($this->getProperties()->getPosition1()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition1()->getFloorZ());
        $position2 = new Vector3($this->getProperties()->getPosition2()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition2()->getFloorZ());
        $position3 = new Vector3($this->getProperties()->getPosition1()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition2()->getFloorZ());
        $position4 = new Vector3($this->getProperties()->getPosition2()->getFloorX(), $player->getPosition()->getFloorY(), $this->getProperties()->getPosition1()->getFloorZ());
        for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position1->getFloorX(), $i, $position1->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId(BlockFactory::getInstance()->get(BlockLegacyIds::AIR, 0)->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position2->getFloorX(), $i, $position2->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId(BlockFactory::getInstance()->get(BlockLegacyIds::AIR, 0)->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position3->getFloorX(), $i, $position3->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId(BlockFactory::getInstance()->get(BlockLegacyIds::AIR, 0)->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position4->getFloorX(), $i, $position4->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId(BlockFactory::getInstance()->get(BlockLegacyIds::AIR, 0)->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    private function setEdit(HCFPlayer $player, bool $edit = false) : void
    {
        $pk = AdventureSettingsPacket::create(AdventureSettingsPacket::DOORS_AND_SWITCHES, AdventureSettingsPacket::PERMISSION_NORMAL, -1, PlayerPermissions::VISITOR, 0, $player->getId());;
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    /**
     * @return ClaimType
     */
    public function getClaimType(): ClaimType
    {
        return $this->claimType;
    }

    /**
     * @return ClaimProperties
     */
    public function getProperties(): ClaimProperties
    {
        return $this->properties;
    }
}