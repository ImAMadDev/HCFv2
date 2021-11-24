<?php

namespace ImAMadDev\manager;

use pocketmine\utils\{Config, SingletonTrait, TextFormat};

use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;

class FactionManager {
    use SingletonTrait;
	
	public static ?HCF $main = null;
	public static array $factions = [];
	
	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$this->init();
	}
	
	public function getFactions() : array {
		return self::$factions;
	}
	
	public function getFaction(?string $name) : ? Faction {
		if($name === null) return null;
		return self::$factions[$name] ?? null;
	}
	
	public function isFaction($faction) : bool {
		if($faction instanceof Faction)  $faction = $faction->getName();
		return in_array($faction, array_keys(self::$factions));
	}
	
	public function equalFaction($faction1, $faction2) : bool {
		if($faction1 instanceof Faction)  $faction1 = $faction1->getName();
		if($faction2 instanceof Faction)  $faction2 = $faction2->getName();
		if($faction1 === null && $faction2 === null) return false;
		return ($faction1 === $faction2);
	}
	
	private function init() : void {
		if(!is_dir(self::$main->getDataFolder() . "factions/")) {
			@mkdir(self::$main->getDataFolder() . "factions/");
		}
		foreach(glob(self::$main->getDataFolder() . "factions/" . "*.yml") as $file) {
			$config = new Config($file, Config::YAML);
			self::$factions[basename($file, ".yml")] = new Faction(self::$main, $config);
			$data = ["name" => basename($file, ".yml"), "x1" => $config->get("x1"), "z1" => $config->get("z1"), "x2" => $config->get("x2"), "z2" => $config->get("z2"), "level" => $config->get("level")];
			$claim = new Claim(HCF::getInstance(), $data);
			if($data['x1'] && $data['x2'] !== null) {
			    ClaimManager::getInstance()->addClaim($claim);
			    self::$main->getLogger()->info(TextFormat::GREEN."Claim » {$claim->getName()} was loaded successfully!");
			}
		}
	}
	
	public function createFaction(array $data, HCFPlayer $player) : bool {
		if($this->validate($data)){
			$config = new Config(self::$main->getDataFolder() . "factions/" . $data['name'] . ".yml", Config::YAML);
			foreach($data as $key => $value) {
				$config->set($key, $value);
			}
			$config->save();
			self::$factions[$data['name']] = new Faction(self::$main, $config);

            $player->getCache()->setInData('faction', $data['name']);
			$player->setFaction(self::$factions[$data['name']]);
			return true;
		} else {
			return false;
		}
	}
	
	public function validate(array $contents): bool{
		if(empty($contents)) return false;
		$needs = ["name", "leader", "coleaders", "members", "allys", "points", "kills", "dtr", "level", "home", "x1", "z1", "x2", "z2"];
		$errors = 0;
		foreach($needs as $need){
			if(!in_array($need, array_keys($contents))){ //!isset($contents[$need])) {
				$errors++;
			}
		}
		return $errors === 0;
	}
	
	public function disband(string $name) : void {
		if(is_file(self::$main->getDataFolder() . "factions/" . $name . ".js")) {
			@unlink(self::$main->getDataFolder() . "factions/" . $name . ".js");
		}
		unset(self::$factions[$name]);
	}
	
	public function getTopPower(): string{
		$points = [];
		foreach(self::$factions as $faction) {
			$points[$faction->getName()] = $faction->getPoints();
		}
		arsort($points);
		$top = 0;
		$message = TextFormat::DARK_GREEN . TextFormat::BOLD . "FACTIONS LEADERBOARD " . TextFormat::RESET . TextFormat::EOL;
		foreach($points as $name => $count){
			if($count <= 0 || $top === 10) break;
			$top++;
			$message .= TextFormat::BOLD . TextFormat::AQUA . $top . TextFormat::RESET . TextFormat::DARK_GREEN . $name . TextFormat::DARK_GRAY . " | " . TextFormat::GREEN . "$" . $count;
		}
		return $message;
	}
	
}