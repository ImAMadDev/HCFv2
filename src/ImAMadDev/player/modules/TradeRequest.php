<?php

namespace ImAMadDev\player\modules;

use ImAMadDev\manager\TradeManager;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\trade\TradeSession;
use JetBrains\PhpStorm\Pure;
use pocketmine\utils\TextFormat;

class TradeRequest
{

    private bool $canceled = false;

    public function __construct(
        private HCFPlayer $sender,
        private HCFPlayer $receiver
    ){
        $this->receiver?->sendMessage(TextFormat::GREEN . "You have received a trade request from " . $this->sender?->getName());
        $this->sender?->sendMessage(TextFormat::GREEN . "You have send a trade request to " . $this->receiver?->getName());
    }

    /**
     * @return HCFPlayer
     */
    public function getSender(): HCFPlayer
    {
        return $this->sender;
    }

    /**
     * @param HCFPlayer $sender
     */
    public function setSender(HCFPlayer $sender): void
    {
        $this->sender = $sender;
    }

    #[Pure] public function isSender(HCFPlayer | string $player) : bool
    {
        if ($player instanceof HCFPlayer) $player = $player->getName();
        return $player == $this->getSender()?->getName();
    }

    #[Pure] public function isReceiver(HCFPlayer | string $player) : bool
    {
        if ($player instanceof HCFPlayer) $player = $player->getName();
        return $player == $this->getReceiver()?->getName();
    }

    public function continue() : void
    {
        TradeManager::getInstance()->addSession(new TradeSession($this->getSender(), $this->getReceiver()));
        TradeManager::getInstance()->removeRequest($this);
    }

    public function cancel() : void
    {
        $this->canceled = true;
        TradeManager::getInstance()->removeRequest($this);
    }

    public function __destruct()
    {
        if ($this->canceled) {
            $this->getSender()->sendMessage(TextFormat::RED . "Your trade request has closed, because player is disconnected");
        }
    }

    /**
     * @return HCFPlayer
     */
    public function getReceiver(): HCFPlayer
    {
        return $this->receiver;
    }

}