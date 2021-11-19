<?php

namespace CombatLogger;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class CombatManager{

	public array $taggedPlayers = [];
	public array $tagged = [];
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
			$this->taggedPlayers[$player] = $time;
			$this->tagged[$player] = $attacker;
		} else {
			unset($this->taggedPlayers[$player]);
			unset($this->tagged[$player]);
		}
	}

	/**
	 * @param string|Player $player
	 *
	 * @return bool
	 */
	#[Pure] public function isTagged(Player|string $player): bool
    {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return isset($this->taggedPlayers[$player]);
	}

	/**
	 * @param string|Player $player
	 *
	 * @return int
	 */
	#[Pure] public function getTagDuration(Player|string $player): int
    {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return ($this->isTagged($player) ? $this->taggedPlayers[$player] : 0);
	}
	
	/**
	 * @param string|Player $player
	 *
	 * @return string
	 */
	#[Pure] public function getTagDamager(Player|string $player): ?string
    {
		if($player instanceof HCFPlayer) $player = $player->getName();
		return ($this->isTagged($player) ? $this->tagged[$player] : null);
	}

}
