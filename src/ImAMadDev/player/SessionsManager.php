<?php

namespace ImAMadDev\player;

use ImAMadDev\player\sessions\{ClassEnergy,
    PlayerRegion,
    Vote,
    ChatMode,
    ClaimSession,
    ArcherMark,
    EnderpearlHistory,
    TraderPlayer};

use ImAMadDev\player\modules\{
	ViewClaim
};

use ImAMadDev\faction\Faction;

trait SessionsManager
{
	private Vote $vote_session;
	private PlayerRegion $region_session;
	private ChatMode $chatMode;
	private ?ClaimSession $claimSession = null;
	private ClassEnergy $class_energy;
	private ArcherMark $archerMark;
	private ViewClaim $claimView;
	private EnderpearlHistory $enderpearlH;
	private ?Focus $focus = null;
	private ?Cooldowns $cooldown = null;
	private TraderPlayer $traderPlayer;
	
	public function setup(): void 
	{
		$this->vote_session = new Vote($this);
		$this->region_session = new PlayerRegion($this);
		$this->chatMode = new ChatMode(PlayerUtils::PUBLIC);
		$this->class_energy = new ClassEnergy($this);
		$this->archerMark = new ArcherMark($this);
		$this->claimView = new ViewClaim($this);
		$this->enderpearlH = new EnderpearlHistory($this);
		$this->traderPlayer = new TraderPlayer($this);
		$this->cooldown = new Cooldowns($this);
	}
	
	/**
     * @return Vote
     */
	public function getVote(): Vote 
	{
		return $this->vote_session;
	}
	
	/**
     * @return Cooldowns
     */
	public function getCooldown() : Cooldowns {
		if($this->cooldown === null) $this->cooldown = new Cooldowns($this);
		return $this->cooldown;
	}
	
	/**
     * @return Focus | null
     */
	public function getFocus() : ?Focus {
		if($this->focus == null) return null;
		return $this->focus;
	}
	
	/**
     * @return Faction | null
     */
	public function setFocus(?Faction $faction = null) : void {
		if($faction === null) {
			$this->focus = null;
			return;
		}
		$this->focus = new Focus($faction);
	}
	
	/**
     * @return ClaimSession | null
     */
	public function setClaimSession(?ClaimSession $session = null) : void {
		$this->claimSession = $session;
	}
	
	/**
     * @return ClaimSession | null
     */
	public function getClaimSession() : ?ClaimSession {
		return $this->claimSession;
	}
	
	/**
     * @return PlayerRegion
     */
	public function getRegion() : PlayerRegion {
		return $this->region_session;
	}
	
	/**
     * @return ChatMode
     */
	public function getChatMode() : ChatMode {
		return $this->chatMode;
	}
	
	/**
     * @return ClassEnergy
     */
	public function getClassEnergy() : ClassEnergy {
		return $this->class_energy;
	}
	
	/**
     * @return EnderpearlHistory
     */
	public function getEnderpearlHistory() : EnderpearlHistory {
		return $this->enderpearlH;
	}
	
	/**
     * @return ArcherMark
     */
	public function getArcherMark() : ArcherMark {
		return $this->archerMark;
	}
	
	/**
     * @return ViewClaim
     */
    public function getClaimView(): ViewClaim
    {
        return $this->claimView;
    }
    
    /**
     * @return TraderPlayer
     */
    public function getTraderPlayer(): TraderPlayer
    {
        return $this->traderPlayer;
    }
}