<?php

namespace ImAMadDev\player;

use pocketmine\utils\TextFormat;

class Cooldowns {
	
	private array $cooldowns = [];
	
	private array $defaultTime = [];

    public function __construct(
        public HCFPlayer $player
    ){}

    public function add(string $name, int $duration = 16) : void {
		$this->cooldowns[strtolower($name)] = time();
		$this->defaultTime[strtolower($name)] = $duration;
        if ($this->player instanceof HCFPlayer) $this->player->sendMessage(TextFormat::RED . "You have entered the " . TextFormat::GOLD . $name . TextFormat::RED . " countdown for " . gmdate('i:s', $duration));
	}
	
	public function reduce(string $name, int $duration = 16) : void {
		$this->defaultTime[strtolower($name)] -= $duration;
	}
	
	public function remove(string $name) : void {
		if(isset($this->cooldowns[strtolower($name)])) unset($this->cooldowns[strtolower($name)]);
		if(isset($this->defaultTime[strtolower($name)])) unset($this->defaultTime[strtolower($name)]);
        if ($this->player instanceof HCFPlayer) $this->player->sendMessage(TextFormat::GREEN . "Your " . TextFormat::GOLD . $name . TextFormat::GREEN . " countdown has expired!");
	}
	
	public function has(string $name) : bool {
		$remaining = 0;
		if(isset($this->cooldowns[strtolower($name)])) {
			$remaining = ($this->defaultTime[strtolower($name)] - (time() - $this->cooldowns[strtolower($name)]));
		}
		return $remaining > 0;
	}
	
	public function get(string $name) : int {
		$remaining = 0;
		if(isset($this->cooldowns[strtolower($name)])) {
			$remaining = ($this->defaultTime[strtolower($name)] - (time() - $this->cooldowns[strtolower($name)]));
		}
		return $remaining;
	}
	
	public function getAll() : array {
		return $this->cooldowns;
	}
	
}