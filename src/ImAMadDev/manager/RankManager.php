<?php

namespace ImAMadDev\manager;

use JsonException;
use pocketmine\utils\Config;

use ImAMadDev\HCF;
use ImAMadDev\rank\RankClass;

class RankManager {
	
	public static ?HCF $main = null;
	public static array $ranks = [];
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$this->init();
	}
	
	public function getRanks() : array {
		return self::$ranks;
	}
	
	public function getRank(?string $name) : ? RankClass {
		if($name === null) return null;
		return self::$ranks[$name] ?? null;
	}
	
	public function isRank($rank) : bool {
		if($rank instanceof RankClass)  $rank = $rank->getName();
		return in_array($rank, array_keys(self::$ranks));
	}
	
	public function equalRank($rank1, $rank2) : bool {
		if($rank1 instanceof RankClass)  $rank1 = $rank1->getName();
		if($rank2 instanceof RankClass)  $rank2 = $rank2->getName();
		return ($rank1 === $rank2);
	}
	
	private function init() : void {
		if(!is_dir(self::$main->getDataFolder() . "ranks/")) {
			@mkdir(self::$main->getDataFolder() . "ranks/");
		}
		foreach(glob(self::$main->getDataFolder() . "ranks/" . "*.yml") as $file) {
			$config = new Config($file, Config::YAML);
			if($this->validate($config->getAll())){
				self::$ranks[$config->get('name')] = new RankClass(self::$main, $config->getAll());
			} else {
				$this->disband(basename($file, ".yml"));
			}
		}
	}

    /**
     * @throws JsonException
     */
    public function createRank(array $data) : void {
		if($this->validate($data)){
			self::$ranks[$data['name']] = new RankClass(self::$main, $data);
			$config = new Config(self::$main->getDataFolder() . "ranks/" . $data['name'] . ".yml", Config::YAML);
			foreach($data as $key => $value) {
				$config->set($key, $value);
			}
			$config->save();
		}
	}
	
	public function validate(array $contents): bool{
		if(empty($contents)) return false;
		$needs = ["name", "format", "tag", "permissions"];
		$errors = 0;
		foreach($needs as $need){
			if(!in_array($need, array_keys($contents))){ //!isset($contents[$need])) {
				$errors++;
			}
		}
		return $errors === 0;
	}
	
	public function disband(string $name) : void {
		if(is_file(self::$main->getDataFolder() . "ranks/" . $name . ".yml")) {
			@unlink(self::$main->getDataFolder() . "ranks/" . $name . ".yml");
		}
		unset(self::$ranks[$name]);
	}
	
}