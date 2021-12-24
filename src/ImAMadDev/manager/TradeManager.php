<?php

namespace ImAMadDev\manager;

use ImAMadDev\HCF;
use ImAMadDev\player\modules\TradeRequest;
use ImAMadDev\trade\TradeSession;
use ImAMadDev\trade\ticks\TradeHeartbeatTask;
use pocketmine\utils\SingletonTrait;

class TradeManager {
    use SingletonTrait;
	
	private static ?HCF $main = null;
	
	private array $sessions = [];

    private array $requests = [];
	
	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$main->getScheduler()->scheduleRepeatingTask(new TradeHeartbeatTask($this), 20);
	}

    /**
     * @return HCF|null
     */
    public static function getMain(): ?HCF
    {
        return self::$main;
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

    public function addRequest(TradeRequest $request): void {
        $this->requests[spl_object_hash($request)] = $request;
    }

    public function removeRequest(TradeRequest $key): void {
        if(isset($this->requests[spl_object_hash($key)])) {
            unset($this->requests[spl_object_hash($key)]);
        }
    }

    public function getRequests(): array {
        return $this->requests;
    }

    public function hasPlayersRequest(string $player1, string $player2) : bool
    {
        foreach ($this->getRequests() as $request) {
            if ($request->isReceiver($player1) and $request->isSender($player2) ||
                $request->isReceiver($player2) and $request->isSender($player1)) {
                return true;
            }
        }
        return false;
    }
}