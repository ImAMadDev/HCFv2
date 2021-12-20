<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\player\modules\TradeRequest;

class TraderPlayer
{

    private bool $hasSession = false;

    private ?TradeRequest $request = null;

    public function __construct(
        private HCFPlayer $player
    ){
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    /**
     * @return bool
     */
    public function hasSession(): bool
    {
        return $this->hasSession;
    }

    /**
     * @param bool $hasSession
     */
    public function setHasSession(bool $hasSession): void
    {
        $this->hasSession = $hasSession;
    }

}