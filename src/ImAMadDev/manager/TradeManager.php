<?php

namespace ImAMadDev\manager;

use ImAMadDev\HCF;
use ImAMadDev\trade\TradeSession;
use ImAMadDev\trade\ticks\TradeHeartbeatTask;

class TradeManager {
	
	private static ?HCF $main = null;
	
	private array $sessions = [];
	
	public function __construct(HCF $main) {
		self::$main = $main;
		$main->getScheduler()->scheduleRepeatingTask(new TradeHeartbeatTask($this), 20);
	}
	
	public function addSession(TradeSession $session): void {
		$this->sessions[] = $session;
	}
	
	public function removeSession(int $key): void {
		if(isset($this->sessions[$key])) {
			unset($this->sessions[$key]);
		}
	}
	
	public function getSessions(): array {
		return $this->sessions;
	}
}