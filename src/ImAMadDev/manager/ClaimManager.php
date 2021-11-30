<?php

namespace ImAMadDev\manager;

use pocketmine\utils\{Config, SingletonTrait, TextFormat};
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
		if(!is_dir(self::$main->getDataFolder() . "opClaims/")) {
			@mkdir(self::$main->getDataFolder() . "opClaims/");
		}
		foreach(glob(self::$main->getDataFolder() . "opClaims/" . "*.yml") as $file) {
			$config = new Config($file, Config::YAML);
			$data = ["name" => basename($file, ".yml"), "x1" => $config->get("x1"), "z1" => $config->get("z1"), "x2" => $config->get("x2"), "z2" => $config->get("z2"), "level" => $config->get("level")];
			$claim = new Claim(HCF::getInstance(), $data);
			if($data['x1'] && $data['x2'] !== null) {
				$this->addClaim($claim);
				self::$main->getLogger()->info(TextFormat::GREEN."Claim » {$claim->getName()} was loaded successfully!");
			}
		}
	}
	
	public function createClaim(Claim $claim) : void {
		$config = new Config(self::$main->getDataFolder() . "opClaims/" . $claim->getName() . ".yml", Config::YAML);
		foreach($claim->getAllArray() as $key => $value) {
			$config->set($key, $value);
		}
		$config->save();
		$this->addClaim($claim);
	}

	
	public function getClaims() : array {
		return self::$claims;
	}
	
	public function getClaim(string $name) : ? Claim {
		foreach(self::$claims as $claim) {
			if($claim->getName() === $name) {
				return $claim;
			}
		}
		return null;
	}
	
	public function isClaim($claim) : bool {
		if($claim instanceof Claim)  $claim = $claim->getName();
		return in_array($claim, array_keys(self::$claims));
	}
	
	public function equalClaim($claim1, $claim2) : bool {
		if($claim1 instanceof Claim) $claim1 = $claim1->getName();
		if($claim2 instanceof Claim) $claim2 = $claim2->getName();
		return ($claim1 === $claim2);
	}
	
	public function addClaim(Claim $claim) : void {
		self::$claims[$claim->getName()] = $claim;
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
		unset(self::$claims[$claim->getName()]);
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
			return $claim->getName();
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