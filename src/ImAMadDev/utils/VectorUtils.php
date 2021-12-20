<?php

namespace ImAMadDev\utils;

use JetBrains\PhpStorm\Pure;
use pocketmine\Server;
use pocketmine\math\{Vector3, AxisAlignedBB};
use pocketmine\world\Position;

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
}