<?php

namespace ImAMadDev\texts;

use pocketmine\world\{World, Position};
use pocketmine\math\AxisAlignedBB;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use floatingtext\FloatingTextApi;

class Texts {
	
	public HCF $main;
	
	public array $data;
	
	public Position $position;
	
	public int $textID;
	
	public string $text;
	
	public array $send = [];
	
	public function __construct(HCF $main, array $data) {
		$this->main = $main;
		$this->data = $data;
		$world = $main->getServer()->getWorldManager()->getWorldByName($this->data['level']);
		$this->position = new Position($this->data['x'], $this->data['y'], $this->data['z'], $world);
		$this->textID = $data['id'];
		$this->text = $data['text'];
	}
	
	public function getName() : string {
		return $this->data['name'];
	}
	
	public function getWorldName() : string {
		return $this->data['level'];
	}
	
	public function getText() : string {
		return $this->text;
	}
/*
	public function onTick() : void {
		foreach($this->getNearByPlayers(60, 60) as $player) {
			if(!in_array($player->getName(), array_keys($this->send))) {
				$message = str_replace("{player}", $player->getName(), $this->getText());
				FloatingTextApi::sendText($this->textID, $player, $message);
				$this->send[$player->getName()] = $player;
			}
		}
		foreach($this->main->getServer()->getOnlinePlayers() as $player) {
			if(in_array($player->getName(), array_keys($this->send)) && round($player->distance($this->position)) > 200) {
				FloatingTextApi::removeText($this->textID, $player);
				unset($this->send[array_search($player->getName(), $this->send)]);
			}
		}
	}
	*/
	public function onTick() : void {
		foreach($this->main->getServer()->getOnlinePlayers() as $player) {
			if(!in_array($player->getName(), array_keys($this->send)) && stripos($player->getRegion(), "Spawn") !== false) {
				$message = str_replace("{player}", $player->getName(), $this->getText());
				FloatingTextApi::sendText($this->textID, $player, $message);
				$this->send[$player->getName()] = $player;
			}
		}
		foreach($this->send as $name => $player) {
			if($this->main->getServer()->getPlayerByPrefix($name) instanceof HCFPlayer) {
				continue;
			}
			unset($this->send[$name]);
		}
	}
	
	public function getNearbyPlayers(int $dictance, int $up) : array {
		$players = [];
		foreach($this->position->getWorld()->getNearbyEntities(new AxisAlignedBB($this->position->getFloorX() - $dictance, $this->position->getFloorY() - $up, $this->position->getFloorZ() - $dictance, $this->position->getFloorX() + $dictance, $this->position->getFloorY() + $up, $this->position->getFloorZ() + $dictance)) as $e){
			if($e instanceof HCFPlayer){
				$players[] = $e;
			}
		}
		return $players;
	}
}