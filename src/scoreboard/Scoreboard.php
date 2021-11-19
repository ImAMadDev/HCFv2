<?php

namespace scoreboard;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;

use JetBrains\PhpStorm\Pure;
use pocketmine\network\mcpe\protocol\{RemoveObjectivePacket, SetDisplayObjectivePacket, SetScorePacket};
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard {

	/** @var array */
	protected static array $scoreboards = [];

    /**
     * @param HCFPlayer $player
     * @param String $objectiveName
     * @param String $displayName
     * @return void
     */
	public function newScoreboard(HCFPlayer $player, string $objectiveName, string $displayName) : void { 
		if(isset(self::$scoreboards[$player->getName()])){
			unset(self::$scoreboards[$player->getName()]);
		}
		$pk = SetDisplayObjectivePacket::create(SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR, $objectiveName, $displayName, "dummy", SetDisplayObjectivePacket::SORT_ORDER_ASCENDING);
		$player->getNetworkSession()->sendDataPacket($pk);
		self::$scoreboards[$player->getName()] = $objectiveName;
	}

    /**
     * @param HCFPlayer $player
     * @return void
     */
	public function remove(HCFPlayer $player) : void {
		if(isset(self::$scoreboards[$player->getName()])){
			$objectiveName = $this->getObjectiveName($player);
			$pk = RemoveObjectivePacket::create($objectiveName);
			$player->getNetworkSession()->sendDataPacket($pk);
			unset(self::$scoreboards[$player->getName()]);
		}
	}

    /**
     * @param HCFPlayer $player
     * @param Int $score
     * @param string|null $message
     * @return void
     */
	public function setLine(HCFPlayer $player, int $score, ?string $message) : void {
		if(!isset(self::$scoreboards[$player->getName()])){
			HCF::getInstance()->getLogger()->info("Error");
			return;
		}
		if($score > 15){
			HCF::getInstance()->getLogger()->info("Error, you exceeded the limit of parameters 1-15");
			return;
		}
		$objectiveName = $this->getObjectiveName($player);
		$entry = new ScorePacketEntry();
		$entry->objectiveName = $objectiveName;
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;

        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
		$player->getNetworkSession()->sendDataPacket($pk);
	}

    /**
     * @param HCFPlayer $player
     * @return string|null
     */
	#[Pure] public function getObjectiveName(HCFPlayer $player) : ?string {
		return self::$scoreboards[$player->getName()] ?? null;
	}
}

		
		
		
		
		