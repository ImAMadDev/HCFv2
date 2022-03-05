<?php

namespace ImAMadDev\faction\ticks;

use ImAMadDev\claim\Claim;
use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\HCF;
use ImAMadDev\utils\VectorUtils;

use pocketmine\player\XboxLivePlayerInfo;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\sound\EndermanTeleportSound;
use pocketmine\world\World;
use pocketmine\world\WorldManager;

class StuckTask extends Task {

	private Position $lastPos;

	private HCFPlayer $player;
	
	private int $claimSize = 200;

	public function __construct(HCFPlayer $player) {
		$this->player = $player;
		$this->lastPos = clone $player->getPosition();
		$this->claimSize = ClaimManager::getInstance()->getClaimByPosition($this->lastPos) instanceof Claim ? ClaimManager::getInstance()->getClaimByPosition($this->lastPos)?->getSize() : 200;
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
			//if(($vec = $this->getnearestsafeposition()) instanceof Vector3){
			if(($vec = VectorUtils::getStuck($player, $this->lastPos)) instanceof Vector3){
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
			if($this->checkMovement($player->getPosition()->getFloorX(), $player->getPosition()->getFloorY(), $player->getPosition()->getFloorZ())){
				$player->getCooldown()->remove('stuck_teleport');
                $this->getHandler()->cancel();
			}
			if(!$player->getCooldown()->has('stuck_teleport')){
                $this->getHandler()->cancel();
			}
		}
	}

    /*
    public function nearestSafeLocation() : ? Vector3 {
        $atPos = clone $this->lastPos;
        $atNeg = clone $this->lastPos;
        for ($xPos = 2, $xNeg = -2; $xPos < 250; $xPos += 2, $xNeg -= 2) {
            for ($zPos = 2, $zNeg = -2; $zPos < 250; $zPos += 2, $zNeg -= 2) {
                $atPos->add($xPos, 0, $zPos);
                var_dump("pos: " . $atPos->__toString());
                if (ClaimManager::getInstance()->getClaimByPosition($atPos) == null) {
                    $y = $this->player->getWorld()->getHighestBlockAt($xPos, $zPos);
                    return new Vector3 ($atPos->x, ($y + 1), $atPos->z);
                }
                $atNeg->subtract($xNeg, 0, $zNeg);
                var_dump("neg: " . $atNeg->__toString());
                if (ClaimManager::getInstance()->getClaimByPosition($atNeg) == null) {
                    $y = $this->player->getWorld()->getHighestBlockAt($xNeg, $zNeg);
                    return new Vector3 ($atNeg->x, ($y + 1), $atNeg->z);
                }
            }
        }

        return null;
    }

    public function getNearestSafePosition(int $searchRadius = 100): ?Vector3
    {
        $minX = $this->lastPos->getFloorX() - $searchRadius;
        $maxX = $this->lastPos->getFloorX() + $searchRadius;
        $minZ = $this->lastPos->getFloorZ() - $searchRadius;
        $maxZ = $this->lastPos->getFloorZ() + $searchRadius;
        $atPos = clone $this->lastPos;
        $atNeg = clone $this->lastPos;
        for ($x = $minX; $x < $maxX; ++$x) {
            for ($z = $minZ; $z < $maxZ; ++$z) {
                $atPos->add($x, 0, $z);
                var_dump("pos: " . $atPos->__toString());
                $factionAtPos = ClaimManager::getInstance()->getClaimByPosition($atPos);
                if (!$factionAtPos instanceof Claim){
                    $y = $this->player->getWorld()->getHighestBlockAt($x, $z);
                    return new Vector3 ($atPos->x, ($y + 1), $atPos->z);
                }
                $atNeg->add($x, 0, $z);
                $factionAtNeg = ClaimManager::getInstance()->getClaimByPosition($atNeg);
                if (!$factionAtNeg instanceof Claim){
                    $y = $this->player->getWorld()->getHighestBlockAt($x, $z);
                    return new Vector3 ($atNeg->x, ($y + 1), $atNeg->z);
                }
            }
        }
        return null;
    }*/
	
	public function getNearestSafePosition():? Vector3{
		$size = $this->claimSize + 1;
        $minimumX = $this->lastPos->getFloorX() - $size;
        $minimumZ = $this->lastPos->getFloorZ() - $size;
        $maximumX = $this->lastPos->getFloorX() + $size;
        $maximumZ = $this->lastPos->getFloorZ() + $size;
		for($x_ = $this->lastPos->getFloorX(); $x_ <= $maximumX; $x_++){
			for($z_ = $this->lastPos->getFloorZ(); $z_ <= $maximumZ; $z_++){
				if($this->checkClaim(new Position($z_, 0, $z_, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($x_, $z_);
                    return new Vector3 ($x_, ($yL + 1), $z_);
				}
			}
		}
		for($x_ = $this->lastPos->getFloorX(); $x_ >= $minimumX; $x_--){
			for($z_ = $this->lastPos->getFloorZ(); $z_ >= $minimumZ; $z_--){
                if($this->checkClaim(new Position($x_, 0, $z_, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($x_, $z_);
                    return new Vector3 ($x_, ($yL + 1), $z_);
				}
			}
		}
		for($x_ = $this->lastPos->getFloorX(); $x_ >= $minimumX; $x_--){
			for($z_ = $this->lastPos->getFloorZ(); $z_ <= $maximumZ; $z_++){
                if($this->checkClaim(new Position($x_, 0, $z_, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($x_, $z_);
                    return new Vector3 ($x_, ($yL + 1), $z_);
				}
			}
		}
		for($x_ = $this->lastPos->getFloorX(); $x_ <= $maximumX; $x_++){
			for($z_ = $this->lastPos->getFloorZ(); $z_ >= $minimumZ; $z_--){
                if($this->checkClaim(new Position($x_, 0, $z_, $this->lastPos->getWorld()))) {
					continue;
				} else {
					$yL = $this->player->getWorld()->getHighestBlockAt($x_, $z_);
                    return new Vector3 ($x_, ($yL + 1), $z_);
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

    private function checkMovement(int $x, int $y, int $z) : bool {
        $last = $this->lastPos;
        $xDiff = abs($last->getFloorX() - $x);
        $yDiff = abs($last->getFloorY() - $y);
        $zDiff = abs($last->getFloorZ() - $z);
        if (($xDiff > 5) || ($yDiff > 5) || ($zDiff > 5)) {
            $this->player->sendMessage(TextFormat::RED . "You moved more than " . TextFormat::BOLD . '5' . TextFormat::RED . " blocks. " . TextFormat::DARK_PURPLE . "Stuck" . TextFormat::RED . " timer ended.");
            return true;
        }
        return false;
   }

}