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
        if ($this->player instanceof HCFPlayer) {
            if (!$this->has($name)) {
                $this->player->sendMessage(TextFormat::RED . "You have entered the " . TextFormat::colorize(PlayerUtils::$Names[strtolower($name)]) . TextFormat::RED . " countdown for " . gmdate('i:s', $duration));
            }
        }
		$this->cooldowns[strtolower($name)] = time();
		$this->defaultTime[strtolower($name)] = $duration;
	}
	
	public function reduce(string $name, int $duration = 16) : void {
		$this->defaultTime[strtolower($name)] -= $duration;
	}
	
	public function remove(string $name) : void {
		if(isset($this->cooldowns[strtolower($name)])) unset($this->cooldowns[strtolower($name)]);
		if(isset($this->defaultTime[strtolower($name)])) unset($this->defaultTime[strtolower($name)]);
        if ($this->player instanceof HCFPlayer) {
            $this->player->sendMessage(TextFormat::GREEN . "Your " . TextFormat::colorize(PlayerUtils::$Names[strtolower($name)]) . TextFormat::GREEN . " countdown has expired!");
        }
	}
	
	public function has(string $name) : bool {
        if (!isset($this->cooldowns[strtolower($name)])) return false;
		$remaining = 0;
		if(isset($this->cooldowns[strtolower($name)])) {
			$remaining = ($this->defaultTime[strtolower($name)] - (time() - $this->cooldowns[strtolower($name)]));
            if ($remaining < 0){
                $this->remove(strtolower($name));
            }
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