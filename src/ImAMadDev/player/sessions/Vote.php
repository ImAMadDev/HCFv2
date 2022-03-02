<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;

class Vote
{
	
	private bool $checking_for_vote = false;
	
	private bool $vote = false;

    public function __construct(
        public HCFPlayer $player,
    ){}

    /**
     * @return bool
     */
    public function isCheckingForVote() : bool {
		return $this->checking_for_vote;
	}
	
	/**
     * @return bool
     */
	public function hasVoted() : bool {
		return $this->vote;
	}
	
	/**
     * @param bool $checking
     */
	public function setCheckingForVote(bool $checking = true) : void {
		$this->checking_for_vote = $checking;
	}
	
	/**
     * @param bool $vote
     */
	public function setVoted(bool $vote = true) : void {
		$this->vote = $vote;
	}

}