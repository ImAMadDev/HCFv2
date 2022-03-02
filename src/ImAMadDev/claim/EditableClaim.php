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
use pocketmine\utils\TextFormat;
use pocketmine\world\{World, Position};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\koth\KOTHArena;
use ImAMadDev\manager\EOTWManager;
use ImAMadDev\faction\Faction;

class EditableClaim extends Claim {
	
	public function __construct(HCF $main, array $data) {
		parent::__construct($main, $data);
	}
	
	public function canEdit(?Faction $faction): bool 
	{
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
}