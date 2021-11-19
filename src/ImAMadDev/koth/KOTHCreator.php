<?php

namespace ImAMadDev\koth;

use ImAMadDev\player\HCFPlayer;

class KOTHCreator{
	
	public ?HCFPlayer $player = null;
	
	public ?string $name = null;
	
	public array $pos = ["one" => null, "two" => null];
	
	public array $corner = ["one" => null, "two" => null];
	
	public int $time = 300;
	
	public int $keys = 3;

    private string $level;

    public function __construct(HCFPlayer $player, string $arena){
		$this->player = $player;
		$this->name = $arena;
		$this->level = $player->getWorld()->getFolderName();
	}
	
	public function setPos($pos): string{
		$this->pos[$pos] = $this->player->getPosition()->x.":".($this->player->getPosition()->y - 1).":".$this->player->getPosition()->z;
		return "Has puesto la posición {$pos} en las coordenadas ".$this->player->getPosition()->x.":".($this->player->getPosition()->y - 1).":".$this->player->getPosition()->z;
	}
	
	public function setCorner($pos): string{
		$this->corner[$pos] = $this->player->getPosition()->x.":".($this->player->getPosition()->y - 1).":".$this->player->getPosition()->z;
		return "Has puesto el corner {$pos} en las coordenadas ".$this->player->getPosition()->x.":".($this->player->getPosition()->y - 1).":".$this->player->getPosition()->z;
	}
	
	public function setKeys(int $keys) : string {
		$this->keys = $keys;
		return "Has puesto el número de keys a {$keys}";
	}
	
	public function setTime(int $time) : string {
		$this->time = $time;
		return "Has puesto el tiempo del KOTH a " . gmdate('i:s', $time);
	}
}