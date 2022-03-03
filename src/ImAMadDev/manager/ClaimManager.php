<?php

namespace ImAMadDev\manager;

use pocketmine\utils\{Config, Filesystem, SingletonTrait, TextFormat};
use ImAMadDev\claim\ClaimListener;
use ImAMadDev\claim\NonEditableClaim;
use ImAMadDev\claim\utils\ClaimFlags;
use ImAMadDev\claim\utils\EditClaimFlag;
use pocketmine\block\BlockLegacyIds;
use pocketmine\Server;
use pocketmine\world\Position;

use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;

class ClaimManager {
    use SingletonTrait;
	
	public static ?HCF $main = null;
	public static array $claims = [];
	
	public function __construct(?HCF $main = null) {
		self::$main = $main ?? HCF::getInstance();
        self::setInstance($this);
        if ($main instanceof HCF) $this->init();
	}
	
	private function init() : void {
		if(!is_dir(self::$main->getDataFolder() . "opClaims/")) @mkdir(self::$main->getDataFolder() . "opClaims/");
		foreach(glob(self::$main->getDataFolder() . "opClaims/" . "*.yml") as $file) {
            $contents = yaml_parse(Config::fixYAMLIndexes(file_get_contents($file)));
			$claim = new NonEditableClaim(HCF::getInstance(), $contents);
			if($contents['x1'] && $contents['x2'] !== null) {
                $claim->getProperties()->addFlag(ClaimFlags::INTERACT, new EditClaimFlag([3, 58, 61, 62, 54, 205, 218, 145, 146, 116, 130, 154, BlockLegacyIds::ACACIA_DOOR_BLOCK, BlockLegacyIds::DARK_OAK_DOOR_BLOCK, BlockLegacyIds::ACACIA_TRAPDOOR]));
                $claim->getProperties()->addFlag(ClaimFlags::BREAK, new EditClaimFlag());
                $claim->getProperties()->addFlag(ClaimFlags::PLACE, new EditClaimFlag());
				$this->addClaim($claim);
				self::$main->getLogger()->info(TextFormat::GREEN."Claim > {$claim->getProperties()->getName()} was loaded successfully!");
			}
		}
	}
	
	public function createClaim(Claim $claim) : void {
        Filesystem::safeFilePutContents(self::$main->getDataFolder() . "opClaims/" . $claim->getProperties()->getName() . ".yml", $claim->getProperties()->getYamlData());
		$this->addClaim($claim);
	}

	
	public function getClaims() : array {
		return self::$claims;
	}
	
	public function getClaim(string $name) : ? Claim {
		foreach(self::$claims as $claim) {
			if($claim->getProperties()->getName() === $name) {
				return $claim;
			}
		}
		return null;
	}
	
	public function isClaim($claim) : bool {
		if($claim instanceof Claim)  $claim = $claim->getProperties()->getName();
		return in_array($claim, array_keys(self::$claims));
	}
	
	public function equalClaim($claim1, $claim2) : bool {
		if($claim1 instanceof Claim) $claim1 = $claim1->getProperties()->getName();
		if($claim2 instanceof Claim) $claim2 = $claim2->getProperties()->getName();
		return ($claim1 === $claim2);
	}
	
	public function addClaim(Claim $claim) : void {
		self::$claims[$claim->getProperties()->getName()] = $claim;
	}
/*
	public function addClaim(Claim $claim) {
        foreach($claim->getChunkHashes() as $hash) {
            if(isset(self::$claims[$hash])) {
                self::$main->getLogger()->notice(self::$claims[$hash]->getName() . "'s chunk was overwritten by {$claim->getName()}.");
            }
            self::$claims[$hash] = $claim;
        }
    }*/
	
	public function createOPClaim(array $data) : void {
		
	}
	
	public function getClaimByPosition(Position $position) : ? Claim {
		foreach(self::$claims as $claim) {
			if($claim->isInside($position)) {
				return $claim;
			}
		}
		return null;
	}
	
	public function getClaimIntersectsWith(Position $firstPosition, Position $secondPosition) : ? Claim {
		foreach(self::$claims as $claim) {
			if($claim->intersectsWith($firstPosition, $secondPosition)) {
				return $claim;
			}
		}
		return null;
	}
	
	public function disband(Claim $claim) {
		unset(self::$claims[$claim->getProperties()->getName()]);
	}
	/*
	public function disband(Claim $claim) {
		foreach($claim->getChunkHashes() as $hash) {
			unset(self::$claims[$hash]);
		}
	}
	
	public function getClaimByPosition(Position $position): ?Claim {
		$x = $position->getX();
		$z = $position->getZ();
		$hash = Level::chunkHash($x >> 4, $z >> 4);
		if(!isset(self::$claims[$hash])) {
			return null;
		}
		if(self::$claims[$hash]->isInside($position)) {
			return self::$claims[$hash];
		}
		return null;
	}
	*/
	public function getClaimNameByPosition(Position $position): string {
		if(($claim = $this->getClaimByPosition($position)) instanceof Claim) {
			return $claim->getProperties()->getName();
		}
		return "Wilderness";
	}
	
	/*
	public function getClaimNameByPosition(Position $pos) : string {
		foreach(self::$claims as $claim) {
			if($claim->isInside($pos->x, $pos->z, $pos->level)) {
				return $claim->getName();
			}
		}
		return "Wilderness";
	}
	
	public function validate(array $contents): bool{
		if(empty($contents)) return false;
		$needs = ["name", "x1", "z1", "x2", "z2"];
		$errors = 0;
		foreach($needs as $need){
			if(!in_array($need, array_keys($contents))){ //!isset($contents[$need])) {
				$errors++;
			}
		}
		return $errors === 0;
	}/*
	
	public function disband(string $name) : void {
		unset(self::$claims[$name]);
	}
	*/
}