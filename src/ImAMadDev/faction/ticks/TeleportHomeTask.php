<?php

namespace ImAMadDev\faction\ticks;

use ImAMadDev\player\HCFPlayer;

use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\EndermanTeleportSound;

class TeleportHomeTask extends Task {
	
	
	protected ?HCFPlayer $player = null;
	
	protected Position $position;
	
	public function __construct(HCFPlayer $player){
		$this->player = $player;
		$this->position = $player->getPosition();
	}
	
	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
            $this->getHandler()->cancel();
			return;
		}
		if($player->getFaction() === null) {
			$player->getCooldown()->remove('home_teleport');
            $this->getHandler()->cancel();
			return;
		}
		if($player->getFaction()->getHome() === null) {
			$player->getCooldown()->remove('home_teleport');
            $this->getHandler()->cancel();
			return;
		}
		if($player->getCooldown()->has('combattag') && $player->getCooldown()->get('home_teleport') <= 0){
			$player->getCooldown()->remove('home_teleport');
            $this->getHandler()->cancel();
			return;
		}
		if($player->getCooldown()->get('home_teleport') <= 0){
			$player->teleport($player->getFaction()->getHome());
			$player->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
			$player->getCooldown()->remove('home_teleport');
            $this->getHandler()->cancel();
		} else {
			if(round($player->getPosition()->asVector3()->distance($this->position)) >= 1){
				$player->getCooldown()->remove('home_teleport');
				$player->sendMessage(TextFormat::RED . "You moved too much home teleport was canceled!");
                $this->getHandler()->cancel();
				return;
			}
			if($player->getCooldown()->has('combattag')) {
				$player->getCooldown()->remove('home_teleport');
				return;
			}
			if(!$player->getCooldown()->has('home_teleport')){
                $this->getHandler()->cancel();
			}
		}
	}
}