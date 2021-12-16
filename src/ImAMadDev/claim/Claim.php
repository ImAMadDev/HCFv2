<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\AdventureSettingsPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\player\GameMode;
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
	
	public array $data;
	
	public Position $firstPosition;
	
	public Position $secondPosition;

    private ClaimType $claimType;
	
	public function __construct(HCF $main, array $data) {
		$this->main = $main;
		$this->data = $data;
        $this->claimType = new ClaimType($data['claim_type'] ?? 'faction');
		$world = $main->getServer()->getWorldManager()->getWorldByName($this->data['level']);
		$this->firstPosition = new Position((float)$this->data['x1'], 0, (float)$this->data['z1'], $world);
		$this->secondPosition = new Position((float)$this->data['x2'], World::Y_MAX, (float)$this->data['z2'], $world);
	}
	
	public function getName() : string {
		return $this->data['name'];
	}
	
	public function getWorldName() : string {
		return $this->data['level'];
	}
	
	public function isInside(Position $position): bool {
		$world = $position->getWorld();
		$firstPosition = $this->firstPosition;
		$secondPosition = $this->secondPosition;
		$minX = min($firstPosition->getX(), $secondPosition->getX());
		$maxX = max($firstPosition->getX(), $secondPosition->getX());
		$minY = 0;
		$maxY = World::Y_MAX;
		$minZ = min($firstPosition->getZ(), $secondPosition->getZ());
		$maxZ = max($firstPosition->getZ(), $secondPosition->getZ());
		return $minX <= $position->getX() and $maxX >= $position->getFloorX() and $minY <= $position->getY() and
			$maxY >= $position->getY() and $minZ <= $position->getZ() and $maxZ >= $position->getFloorZ() and
            $world->getFolderName() === $this->getWorldName();
	}
	
	#[Pure] public function intersectsWith(Position $firstPosition, Position $secondPosition, float $epsilon = 0.00001) : bool{
		$minX = min($this->firstPosition->getFloorX(), $this->secondPosition->getFloorX());
		$maxX = max($this->firstPosition->getFloorX(), $this->secondPosition->getFloorX());
		$minY = 0;
		$maxY = World::Y_MAX;
		$minZ = min($this->firstPosition->getFloorZ(), $this->secondPosition->getFloorZ());
		$maxZ = max($this->firstPosition->getFloorZ(), $this->secondPosition->getFloorZ());
		if(max($firstPosition->getFloorX(), $secondPosition->getFloorX()) - $minX > $epsilon and $maxX - min($firstPosition->getFloorX(), $secondPosition->getFloorX()) > $epsilon){
			if(max($firstPosition->getFloorY(), $secondPosition->getFloorY()) - $minY > $epsilon and $maxY - min($firstPosition->getFloorY(), $secondPosition->getFloorY()) > $epsilon){
				return max($firstPosition->getFloorZ(), $secondPosition->getFloorZ()) - $minZ > $epsilon and $maxZ - min($firstPosition->getFloorZ(), $secondPosition->getFloorZ()) > $epsilon;
			}
		}
		return false;
	}
	
	public function isFaction() : bool {
		return (HCF::getFactionManager()->isFaction($this->getName()));
	}
	
	public function getFaction() : ? Faction {
		return (HCF::getFactionManager()->getFaction($this->getName()));
	}
	
	public function isKOTH() : bool {
		return ($this->main->getKOTHManager()->arenaExists($this->getName()));
	}
	
	public function getKOTH() : ? KOTHArena {
		return ($this->main->getKOTHManager()->getArena($this->getName()));
	}
	
	public function canEdit(?Faction $faction) : bool {
        if ($this->getClaimType() == ClaimType::KOTH or $this->getClaimType() == ClaimType::SPAWN) return false;
		if($this->isFaction()) {
			$claim = HCF::getFactionManager()->getFaction($this->getName());
			if($claim->getDTR() <= 0) {
				return true;
			}
			if($faction instanceof Faction) {
				if($claim->getName() === $faction->getName()) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} elseif($this->isKOTH()) {
			return false;
		} else {
			if(stripos($this->getName(), "Spawn") !== false or stripos($this->getName(), "Warzone") !== false or stripos($this->getName(), "Road") !== false or stripos($this->getName(), "KOTH") !== false) {
				return false;
			} else {
				return true;
			}
		}
	}
	
	public function join(HCFPlayer $player) : bool {
		$faction = $this->getFaction();
        if ($player->getGamemode() === GameMode::CREATIVE()) return true;
		if($faction !== null) {
			if($player->isInvincible()) {
				return false;
			}
			if($faction->getDTR() <= 0) {
				if($player->getGamemode() === GameMode::ADVENTURE()) {
					$player->setGamemode(GameMode::SURVIVAL());
                    $this->setEdit($player, true);
				}
            } else {
				if($faction->isInFaction($player->getName())) {
					if($player->getGamemode() === GameMode::ADVENTURE()) {
						$player->setGamemode(GameMode::SURVIVAL());
                        $this->setEdit($player, true);
					}
                } else {
					if($player->getGamemode() === GameMode::SURVIVAL()) {
						$player->setGamemode(GameMode::ADVENTURE());
                        $this->setEdit($player);
					}
                }
            }
        } else {
			if(stripos($this->getName(), "Spawn") !== false) {
				if($player->getCooldown()->has('combattag')  && EOTWManager::isEnabled() === false) {
					return false;
				}
            } else {
				if($player->getGamemode() === GameMode::ADVENTURE()) {
					$player->setGamemode(GameMode::SURVIVAL());
                    $this->setEdit($player, true);
				}
            }
        }
        return true;
    }
	
	public function viewMap(HCFPlayer $player){
        $blocks = [BlockFactory::getInstance()->get(BlockLegacyIds::GLASS, 0), BlockFactory::getInstance()->get(BlockLegacyIds::DIAMOND_BLOCK, 0)];
		$position1 = new Vector3($this->firstPosition->getFloorX(), $player->getPosition()->getFloorY(), $this->firstPosition->getFloorZ());
		$position2 = new Vector3($this->secondPosition->getFloorX(), $player->getPosition()->getFloorY(), $this->secondPosition->getFloorZ());
		$position3 = new Vector3($this->firstPosition->getFloorX(), $player->getPosition()->getFloorY(), $this->secondPosition->getFloorZ());
		$position4 = new Vector3($this->secondPosition->getFloorX(), $player->getPosition()->getFloorY(), $this->firstPosition->getFloorZ());
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position1->getFloorX(), $i, $position1->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
			$player->getNetworkSession()->sendDataPacket($pk);
		}
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position2->getFloorX(), $i, $position2->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
		}
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position3->getFloorX(), $i, $position3->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
		}
		for($i = $player->getPosition()->getFloorY(); $i < $player->getPosition()->getFloorY() + 40; $i++){
            $pos = new BlockPosition($position4->getFloorX(), $i, $position4->getFloorZ());
            $block = RuntimeBlockMapping::getInstance()->toRuntimeId($blocks[array_rand($blocks)]->getFullId());
            $pk = UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
		}
	}
	
	#[ArrayShape(['name' => "string", 'x1' => "float|int", 'z1' => "float|int", 'x2' => "float|int", 'z2' => "float|int", 'level' => "mixed"])] public function getAllArray(): array {
		return [
			'name' => $this->getName(),
			'x1' => $this->firstPosition->x,
			'z1' => $this->firstPosition->z,
			'x2' => $this->secondPosition->x,
			'z2' => $this->secondPosition->z,
			'level' => $this->data['level']
		];
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
}