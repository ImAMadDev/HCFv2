<?php

namespace ImAMadDev\faction\ticks;

use ImAMadDev\claim\Claim;
use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\HCF;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\sound\EndermanTeleportSound;

class StuckTask extends Task {

	private Position $lastPos;

	private HCFPlayer $player;

	public function __construct(HCFPlayer $player) {
		$this->player = $player;
		$this->lastPos = clone $player->getPosition();
		HCF::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
            $this->getHandler()->cancel();
			return;
		}
		if(round($player->getPosition()->asVector3()->distance($this->lastPos)) > 4 && $player->getCooldown()->get('stuck_teleport') >= 1){
			$player->getCooldown()->remove('stuck_teleport');
			$player->sendMessage(TextFormat::RED . "You moved too much the stuck was canceled!");
            $this->getHandler()->cancel();
			return;
		}
		if($player->getCooldown()->get('stuck_teleport') <= 0){
			if(($vec = $this->searchStuck($player->getPosition()->x, $player->getPosition()->z)) instanceof Vector3){
				$player->getCooldown()->remove('stuck_teleport');
				$player->teleport($vec);
				$player->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
				$player->sendMessage(TextFormat::GREEN . "You have been transported correctly!");
                $this->getHandler()->cancel();
			} else {
				if($player->getFaction() !== null && $player->getFaction()->getHome() !== null) {
					$player->getCooldown()->remove('stuck_teleport');
					$player->teleport($player->getFaction()->getHome());
					$player->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
					$player->sendMessage(TextFormat::GOLD . "We have not found a safe position so we send you to your home!");
                    $this->getHandler()->cancel();
                }
			}
		} else {
			if(round($player->getPosition()->asVector3()->distance($this->lastPos)) > 4){
				$player->getCooldown()->remove('stuck_teleport');
                $this->getHandler()->cancel();
				return;
			}
			if(!$player->getCooldown()->has('stuck_teleport')){
                $this->getHandler()->cancel();
			}
		}
	}
	
	public function searchStuck($x, $z):? Vector3{
		for($i = $x; $i <= ($x + 200); $i++){
			for($l = $z; $l <= ($z + 200); $l++){
				if($this->checkClaim(new Position($i, 0, $l, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($i, $l);
                    return new Vector3 ($i, ($yL + 1), $l);
				}
			}
		}
		for($i = $x; $i >= ($x - 200); $i--){
			for($l = $z; $l >= ($z - 200); $l--){
                if($this->checkClaim(new Position($i, 0, $l, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($i, $l);
                    return new Vector3 ($i, ($yL + 1), $l);
				}
			}
		}
		for($i = $x; $i >= ($x - 200); $i--){
			for($l = $z; $l <= ($z + 200); $l++){
                if($this->checkClaim(new Position($i, 0, $l, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($i, $l);
                    return new Vector3 ($i, ($yL + 1), $l);
				}
			}
		}
		for($i = $x; $i <= ($x + 200); $i++){
			for($l = $z; $l >= ($z - 200); $l--){
                if($this->checkClaim(new Position($i, 0, $l, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($i, $l);
                    return new Vector3 ($i, ($yL + 1), $l);
				}
			}
		}
		return null;
	}

    public function checkClaim(Position $pos) : bool
    {
        if(($claim = ClaimManager::getInstance()->getClaimByPosition($pos)) instanceof Claim) return $claim->getClaimType()->getType() == ClaimType::FACTION;
        return false;
    }

}