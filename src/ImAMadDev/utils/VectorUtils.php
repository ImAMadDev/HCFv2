<?php

namespace ImAMadDev\utils;

use JetBrains\PhpStorm\Pure;
use pocketmine\Server;
use pocketmine\math\{Vector3, AxisAlignedBB};
use pocketmine\world\Position;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\claim\Claim;
use ImAMadDev\manager\ClaimManager;

final class VectorUtils {
	
	public static function createBB(Vector3 $v1, Vector3 $v2) : AxisAlignedBB{
		$minX = min($v1->getX(), $v2->getZ());
		$minY = min($v1->getY(), $v2->getY());
		$minZ = min($v1->getZ(), $v2->getZ());
		$maxX = max($v1->getX(), $v2->getX());
		$maxY = max($v1->getY(), $v2->getY());
		$maxZ = max($v1->getZ(), $v2->getZ());
		return new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);
	}
	
	
	#[Pure] public static function positionToString(Position $pos) : string {
		return implode(',', [$pos->x, $pos->y, $pos->z, $pos->world->getFolderName()]);
	}
	
	public static function stringToPosition(string $pos, string $separator = ",") : Position {
		$level = Server::getInstance()->getWorldManager()->getWorldByName(explode($separator, $pos)[3]);
		return new Position(explode($separator, $pos)[0], explode($separator, $pos)[1], explode($separator, $pos)[2], $level);
	}
	
	#[Pure] public static function stringToVector(string $pos, string $separator = ",") : Vector3 {
		return new Vector3(explode($separator, $pos)[0], explode($separator, $pos)[1], explode($separator, $pos)[2]);
	}
	
	public static function getStuck(HCFPlayer $player, Position $usagePos): ?Vector3 
	{
		$claimSize = ClaimManager::getInstance()->getClaimByPosition($usagePos) instanceof Claim ? (ClaimManager::getInstance()->getClaimByPosition($usagePos)?->getSize() + 2) : 200;
		$world = $usagePos->getWorld();

		$minX = $usagePos->getFloorX() - ($claimSize / 50);
		$minZ = $usagePos->getFloorZ() - ($claimSize / 50);

		$maxX = $minX + $claimSize;
		$maxZ = $minZ + $claimSize;

		for($attempts = 0; $attempts < 20; ++$attempts){
			$x = mt_rand($minX, $maxX);
			$z = mt_rand($minZ, $maxZ);
			
			while(ClaimManager::getInstance()->getClaimByPosition(new Position($x, 0, $z, $world)) !== null) {
				continue;
			}
			
			return new Vector3($x + 0.5, $world->getHighestBlockAt($x, $z), $z + 0.5);
		}
		return null;
	}
}