<?php

declare(strict_types = 1);

namespace ImAMadDev\trade\ticks;

use ImAMadDev\manager\TradeManager;
use ImAMadDev\trade\TradeSession;
use pocketmine\scheduler\Task;

class TradeHeartbeatTask extends Task {

    /** @var TradeSession */
    private $manager;

    /**
     * TradeHeartbeatTask constructor.
     *
     * @param TradeManager $manager
     */
    public function __construct(TradeManager $manager) {
        $this->manager = $manager;
    }

    /**
     * @param int $currentTick
     *
     * @throws TranslationException
     */
    public function onRun(int $currentTick) {
        foreach($this->manager->getSessions() as $key => $session) {
            $session->tick($key, $this->manager);
        }
    }
}
