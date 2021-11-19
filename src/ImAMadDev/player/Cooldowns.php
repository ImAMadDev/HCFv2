<?php

namespace ImAMadDev\player;

class Cooldowns {
	
	private array $cooldowns = [];
	
	private array $defaultTime = [];
	
	public function add(string $name, int $duration = 16) : void {
		$this->cooldowns[strtolower($name)] = time();
		$this->defaultTime[strtolower($name)] = $duration;
	}
	
	public function reduce(string $name, int $duration = 16) : void {
		$this->defaultTime[strtolower($name)] -= $duration;
	}
	
	public function remove(string $name) : void {
		if(isset($this->cooldowns[strtolower($name)])) unset($this->cooldowns[strtolower($name)]);
		if(isset($this->defaultTime[strtolower($name)])) unset($this->defaultTime[strtolower($name)]);
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