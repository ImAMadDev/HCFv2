<?php

namespace CombatLogger;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class CombatManager{

	public static array $taggedPlayers = [];
	public static array $tagged = [];
	private HCF $main;

	public function __construct(HCF $main) {
		$this->main = $main;
		$this->startHeartbeat();
	}
	/**
	* @return HCF
	*/
	public function getMain(): HCF{
		return $this->main;
	}
	/**
	 * Start the heartbeat task
	 */
	public function startHeartbeat() {
		$this->main->getScheduler()->scheduleRepeatingTask(new TaggedHeartbeatTask($this), 20);
	}

    /**
     * @param string|Player $player
     * @param Player|string $attacker
     * @param bool $value
     * @param int $time
     */
	public function setTagged(Player|string $player, Player|string $attacker, bool $value = true, int $time = 30) {
		if($player instanceof HCFPlayer) $player = $player->getName();
		if($attacker instanceof HCFPlayer) $attacker = $attacker->getName();
		if($value) {
            self::$taggedPlayers[$player] = $time;
            self::$tagged[$player] = $attacker;
		} else {
			unset(self::$taggedPlayers[$player]);
			unset(self::$tagged[$player]);
		}
	}

	/**
	 * @param string|Player $player
	 *
	 * @return bool
	 */
	#[Pure] public static function isTagged(Player|string $player): bool
    {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return isset(self::$taggedPlayers[$player]);
	}

	/**
	 * @param string|Player $player
	 *
	 * @return int
	 */
	#[Pure] public static function getTagDuration(Player|string $player): int
    {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return (self::isTagged($player) ? self::$taggedPlayers[$player] : 0);
	}

    /**
     * @param string|Player $player
     *
     * @return string|null
     */
	#[Pure] public static function getTagAttacker(Player|string $player): ?string
    {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return (self::isTagged($player) ? self::$tagged[$player] : null);
	}

}
