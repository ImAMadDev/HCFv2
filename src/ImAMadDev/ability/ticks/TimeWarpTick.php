<?php

namespace ImAMadDev\ability\ticks;

use ImAMadDev\player\HCFPlayer;

use pocketmine\world\Position;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\Task;

class TimeWarpTick extends Task {
	
	private HCFPlayer  $player;
	
	public function __construct(HCFPlayer $player)
	{
		$this->player = $player;
	}
	
	public function onRun(): void 
	{
		$player = $this->player;
		if(!$player->isOnline()){
            $this->getHandler()->cancel();
            return;
    	}
    	if($player->getEnderpearlHistory()->getUsageLocation() !== null) {
    		$player->teleport($player->getEnderpearlHistory()->getUsageLocation());
    		$player->getEnderpearlHistory()->setUsageLocation(null);
    		$player->sendMessage(TextFormat::colorize("&c» &eYou have been returned to the position where you used the last EnderPearl."));
    	}
    	$this->getHandler()->cancel();
    }
}