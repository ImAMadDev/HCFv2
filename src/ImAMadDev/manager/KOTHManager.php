<?php

declare(strict_types=1);

namespace ImAMadDev\manager;

use ImAMadDev\HCF;
use ImAMadDev\koth\{KOTHCreator, KOTHArena};
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\VectorUtils;
use ImAMadDev\koth\ticks\KOTHTick;
use ImAMadDev\claim\Claim;

use pocketmine\utils\{SingletonTrait, TextFormat, Config};

class KOTHManager{
    use SingletonTrait;

	private array $arenas = [];
	public ?KOTHArena $selected = null;
	public ?HCF $main = null;
	
	public array $creators = [];
	
	public function __construct(HCF $main){
		$this->main = $main;
        self::setInstance($this);
		$main->getScheduler()->scheduleRepeatingTask(new KOTHTick($this), 20);
		$this->intArenas();
	}
	
	public function intArenas(): void{
        @mkdir($this->main->getDataFolder() . "koths/");
		foreach(glob($this->main->getDataFolder() . "koths/" . "*.js") as $kothFile) {
			$data = (new Config($kothFile, Config::JSON))->getAll();
			$this->arenas[$data["name"]] = new KOTHArena($data["name"], $data["pos1"], $data["pos2"], $data["corner1"], $data["corner2"], (int)$data["time"], $data["level"], $data["keys"]);
			$data = ["name" => $data["name"], "x1" => VectorUtils::stringToVector($data["pos1"], ":")->x, "z1" => VectorUtils::stringToVector($data["pos1"], ":")->z, "x2" => VectorUtils::stringToVector($data["pos2"], ":")->x, "z2" => VectorUtils::stringToVector($data["pos2"], ":")->z, "level" => $data["level"]];
			$claim = new Claim(HCF::getInstance(), $data);
			ClaimManager::getInstance()->addClaim($claim);
			$this->main->getLogger()->info(TextFormat::GREEN."KOTH » {$claim->getName()} was loaded successfully!");
		}
	}
	
	public function addCreator(HCFPlayer $player, string $arena): string{
		if(isset($this->creators[$player->getName()])) return "ya estas en creacion";
		$this->creators[$player->getName()] = new KOTHCreator($player, $arena);
		return "haz sido agregado a la creacion de arenas con la arena: ".$arena;
	}
	
	public function creatorExists(HCFPlayer $player): bool{
		if(isset($this->creators[$player->getName()])) return true;
		return false;
	}
	
	public function setPos(string $pos, HCFPlayer $player): string{
		if($pos == 1) {
			return $this->creators[$player->getName()]->setPos("one");
		} elseif($pos == 2) {
			return $this->creators[$player->getName()]->setPos("two");
		} else {
			return "Error: corner 1 o 2, no existe {$pos}";
		}
	}
	
	public function setCorner(string $pos, HCFPlayer $player): string{
		if($pos == 1) {
			return $this->creators[$player->getName()]->setCorner("one");
		} elseif($pos == 2) {
			return $this->creators[$player->getName()]->setCorner("two");
		} else {
			return "Error: corner 1 o 2, no existe {$pos}";
		}
	}
	
	public function setTime(int $time, HCFPlayer $player) : string {
		return $this->creators[$player->getName()]->setTime($time);
	}
	
	public function setKeys(int $keys, HCFPlayer $player) : string {
		return $this->creators[$player->getName()]->setKeys($keys);
	}
	
	public function addArena(HCFPlayer $player): bool{
		$class = $this->creators[$player->getName()];
		$config = new Config($this->main->getDataFolder()."koths/".$class->name.".js", Config::JSON);
		$config->set("name", $class->name);
		$config->set("pos1", $class->pos["one"]);
		$config->set("pos2", $class->pos["two"]);
		$config->set("corner1", $class->corner["one"]);
		$config->set("corner2", $class->corner["two"]);
		$config->set("level", $class->level);
		$config->set("time", $class->time);
		$config->set("keys", $class->keys);
		$config->save();
		$this->arenas[$class->name] = new KOTHArena($class->name, $class->pos["one"], $class->pos["two"], $class->corner["one"], $class->corner["two"], $class->time, $class->level, $class->keys);
		$data = ["name" => $class->name, "x1" => VectorUtils::stringToVector($class->pos["one"], ":")->x, "z1" => VectorUtils::stringToVector($class->pos["one"], ":")->z, "x2" => VectorUtils::stringToVector($class->pos["two"], ":")->x, "z2" => VectorUtils::stringToVector($class->pos["two"], ":")->z, "level" => $class->level];
		$claim = new Claim(HCF::getInstance(), $data);
		ClaimManager::getInstance()->addClaim($claim);
		unset($this->creators[$player->getName()]);
		return true;
	}
	
	public function arenaExists(string $name): bool{
		return isset($this->arenas[$name]);
	}
	
	public function getArena(string $name): ? KOTHArena{
		if(!isset($this->arenas[$name])) return null;
		return $this->arenas[$name];
	}
	
	public function removeArena(string $name): bool{
		if(!isset($this->arenas[$name])) return true;
		$this->getArena($name)->finish();
		@unlink($this->main->getDataFolder()."koths/".$name.".js");
		unset($this->arenas[$name]);
		return true;
	}
	
	public function getArenas(): array{
		return $this->arenas;
	}
	
	public function getKoths(): string{
		$koths = [];
		foreach($this->arenas as $koth){
			$status = $koth->isEnabled() ? "§aActivated" : "§cDeactivated";
			$vec = explode(":", $koth->pos["one"]);
			$koths[] = "§l§6KOTH §r§9".$koth->getName().": §eStatus {$status} §eCoords: §a".round((int)$vec[0]) ." : ".round((int)$vec[2])." §eTime: §a".gmdate("i:s", $koth->time);
		}
		if(count($koths) === 0) return "Sin Koths";
		return implode("\n", $koths);
	}
	
	public function getKoth(string $name): string{
		if(!isset($this->arenas[$name])) return "This koth doesn't exist";
		$koth = $this->arenas[$name];
		$vec = explode(":", $koth->pos["one"]);
		$status = $koth->isEnabled() ? "§aActivate" : "§cDeactivate";
		return "§l§6KOTH §r§9".$koth->getName().": §eStatus {$status} §eCoords: §a".round((int)$vec[0]) ." : ".round((int)$vec[2])." §eTime: §a".gmdate("i:s", $koth->time);
	}
	
	public function getSelected(): ? KOTHArena{
		return $this->selected;
	}
	
	public function selectArena(?string $name = null): void{
		if($name === null){
			$this->selected = null;
			return;
		}
		if(!isset($this->arenas[$name])){
			$this->selected = null;
			return;
		}
		$this->arenas[$name]->enable();
		$this->selected = $this->arenas[$name];
	}
	
}