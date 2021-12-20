<?php

declare(strict_types = 1);

namespace ImAMadDev\trade\ticks;

use ImAMadDev\manager\TradeManager;
use ImAMadDev\trade\TradeSession;
use pocketmine\scheduler\Task;

class TradeHeartbeatTask extends Task {

    private TradeManager $manager;

    /**
     * TradeHeartbeatTask constructor.
     *
     * @param TradeManager $manager
     */
    public function __construct(TradeManager $manager) {
        $this->manager = $manager;
    }

    public function onRun() : void {
        foreach($this->manager->getSessions() as $key => $session) {
            $session->tick($key, $this->manager);
        }
    }
}
