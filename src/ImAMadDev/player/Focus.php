<?php

namespace ImAMadDev\player;

use ImAMadDev\faction\Faction;

class Focus {
	
	private ?Faction $faction;
	
	public function __construct(Faction $faction) {
		$this->faction = $faction;
	}
	
	public function getFaction() : Faction {
		return $this->faction;
	}
}