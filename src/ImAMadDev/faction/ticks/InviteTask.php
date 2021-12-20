<?php

namespace ImAMadDev\faction\ticks;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use ImAMadDev\faction\Faction;

use pocketmine\utils\TextFormat;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class InviteTask extends Task {

	private HCF $main;
	
	private Faction $faction;

	private int $time = 30;

	private string $invited;

	public function __construct(HCF $main, string $invited, Faction $faction) {
		$this->main = $main;
		$this->invited = $invited;
		$this->faction = $faction;
		$main->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	public function onRun() : void {
		$invited = $this->invited;
		if(!$this->faction instanceof Faction){
			$this->getHandler()->cancel();
			return;
		}
		if(($player = Server::getInstance()->getPlayerByPrefix($invited)) instanceof HCFPlayer) {
			if(!$player->isOnline()){
				$this->faction->removeInvite($player);
                $this->getHandler()->cancel();
				return;
			}
			if(!$this->faction->isInvited($player)){
                $this->getHandler()->cancel();
				return;
			}
			if($this->time-- <= 0) {
				$this->faction->removeInvite($player);
				$player->sendMessage(TextFormat::RED . "Your invitation to the faction " . TextFormat::GOLD . $this->faction->getName() . TextFormat::RED . " has expired!");
                $this->getHandler()->cancel();
			}
		} elseif(($faction = $this->main->getFactionManager()->getFaction($invited)) instanceof Faction) {
			if(!$this->main->getFactionManager()->isFaction($faction->getName())) { 
				$this->faction->removeAllyRequest($faction);
                $this->getHandler()->cancel();
				return;
			}
			if(!$this->faction->isAllying($faction)){
                $this->getHandler()->cancel();
				return;
			}
			if($this->time-- <= 0) {
				$this->faction->removeAllyRequest($faction);
				foreach($faction->getOnlineMembers() as $m) {
					if($faction->isLeader($m->getName()) or $faction->isCoLeader($m->getName())) {
						$m->sendMessage(TextFormat::RED . "Your invitation to the faction " . TextFormat::GOLD . $this->faction->getName() . TextFormat::RED . " has expired!");
					}
				}
                $this->getHandler()->cancel();
			}
		}
	}

}