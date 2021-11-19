<?php

namespace ImAMadDev\manager;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;

class ReclaimManager {
	
	public static function getReclaimByPlayer(HCFPlayer $player) : array {
		$keys = [];
		$has = false;
		foreach($player->getRanks() as $rank) {
			switch($rank->getName()){
				case "Anubis":
					if($has === false) {
						$keys[] = CrateManager::getInstance()->getCrateByName('Vote')->getCrateKey(15);
						$keys[] = CrateManager::getInstance()->getCrateByName('Eternal')->getCrateKey(8);
						$keys[] = CrateManager::getInstance()->getCrateByName('Cthulhu')->getCrateKey(6);
						$keys[] = CrateManager::getInstance()->getCrateByName('Sapphire')->getCrateKey(6);
						$keys[] = CrateManager::getInstance()->getCrateByName('Anubis')->getCrateKey(4);
						$has = true;
					}
					break;
				case "Partner":
					if($has === false) {
						$keys[] = CrateManager::getInstance()->getCrateByName('Vote')->getCrateKey(13);
						$keys[] = CrateManager::getInstance()->getCrateByName('Eternal')->getCrateKey(7);
						$keys[] = CrateManager::getInstance()->getCrateByName('Cthulhu')->getCrateKey(5);
						$keys[] = CrateManager::getInstance()->getCrateByName('Sapphire')->getCrateKey(5);
						$keys[] = CrateManager::getInstance()->getCrateByName('Anubis')->getCrateKey(3);
						$has = true;
					}
					break;
				case "Sapphire":
					if($has === false) {
						$keys[] = CrateManager::getInstance()->getCrateByName('Vote')->getCrateKey(13);
						$keys[] = CrateManager::getInstance()->getCrateByName('Eternal')->getCrateKey(6);
						$keys[] = CrateManager::getInstance()->getCrateByName('Cthulhu')->getCrateKey(4);
						$keys[] = CrateManager::getInstance()->getCrateByName('Sapphire')->getCrateKey(3);
						$keys[] = CrateManager::getInstance()->getCrateByName('Anubis')->getCrateKey(2);
						$has = true;
					}
					break;
				case "Cthulhu":
					if($has === false) {
						$keys[] = CrateManager::getInstance()->getCrateByName('Vote')->getCrateKey(10);
						$keys[] = CrateManager::getInstance()->getCrateByName('Eternal')->getCrateKey(4);
						$keys[] = CrateManager::getInstance()->getCrateByName('Cthulhu')->getCrateKey(3);
						$keys[] = CrateManager::getInstance()->getCrateByName('Sapphire')->getCrateKey(1);
						$has = true;
					}
					break;
				case "Eternal":
					if($has === false) {
						$keys[] = CrateManager::getInstance()->getCrateByName('Vote')->getCrateKey(5);
						$keys[] = CrateManager::getInstance()->getCrateByName('Eternal')->getCrateKey(2);
						$keys[] = CrateManager::getInstance()->getCrateByName('Cthulhu')->getCrateKey(1);
						$has = true;
					}
					break;
			}
		}
		return $keys;
	}
}