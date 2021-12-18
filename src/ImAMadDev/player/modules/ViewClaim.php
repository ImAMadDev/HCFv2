<?php

namespace ImAMadDev\player\modules;

use ImAMadDev\claim\Claim;
use ImAMadDev\manager\ClaimManager;
use ImAMadDev\player\HCFPlayer;

class ViewClaim
{

    /**
     * @var Claim|null
     */
    private ?Claim $lastClaim = null;

    /**
     * @var Claim|null
     */
    private ?Claim $currentClaim = null;

    private bool $update = false;

    /**
     * @param HCFPlayer $player
     */
    public function __construct(
        private HCFPlayer $player
    ){}

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    /**
     * @param Claim|null $claim
     * @return void
     */
    public function setLastClaim(?Claim $claim) : void
    {
        $this->lastClaim = $claim;
    }

    /**
     * @return Claim|null
     */
    public function getCurrentClaim(): ?Claim
    {
        return $this->currentClaim;
    }

    /**
     * @param Claim|null $currentClaim
     */
    public function setCurrentClaim(?Claim $currentClaim): void
    {
        $this->currentClaim = $currentClaim;
    }

    /**
     * @return Claim|null
     */
    public function getLastClaim() : ?Claim
    {
        return $this->lastClaim;
    }

    public function update() : void
    {
        if ($this->isUpdate() === false) return;
        if ($this->getPlayer()->getRegion()->get() !== $this->getCurrentClaim()?->getProperties()->getName()){
            $this->setLastClaim($this->getCurrentClaim());
            $claim = $this->getPlayer()->getRegion()->get() === "Wilderness" ? null : ClaimManager::getInstance()->getClaim($this->getPlayer()->getRegion()->get());
            $this->setCurrentClaim($claim);
            $this->getLastClaim()?->cancelMap($this->getPlayer());
            $this->getCurrentClaim()?->viewMap($this->getPlayer());
        }
    }

    /**
     * @return bool
     */
    public function isUpdate(): bool
    {
        return $this->update;
    }

    /**
     * @param bool $update
     */
    public function setUpdate(bool $update): void
    {
        $this->update = $update;
    }

}