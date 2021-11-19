<?php

namespace ImAMadDev\ticks\player;

use ImAMadDev\player\HCFPlayer;

use pocketmine\lang\KnownTranslationKeys;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\utils\TextFormat;

class LogoutTask extends Task {
	
	protected ?HCFPlayer $player = null;
	
	protected Position $position;
	
	public function __construct(HCFPlayer $player){
		$this->player = $player;
		$this->position = $player->getPosition();
	}
	
	public function onRun() : void {
		$player = $this->player;
		if(!$player->isOnline()){
			$this->getHandler()->cancel();
			return;
		}
		if($player->getCooldown()->has('combattag') && $player->getCooldown()->get('logout') <= 0){
			$player->setCanLogout(false);
			$player->getCooldown()->remove('logout');
            $this->getHandler()->cancel();
			return;
		}
		if($player->getCooldown()->get('logout') <= 0){
			$player->setCanLogout(true);
            $player->kick(KnownTranslationKeys::DISCONNECTIONSCREEN_NOREASON);
			$player->getCooldown()->remove('logout');
            $this->getHandler()->cancel();
		} else {
			if(round($player->getPosition()->asVector3()->distance($this->position)) >= 1){
				$player->setCanLogout(false);
				$player->getCooldown()->remove('logout');
				$player->sendMessage(TextFormat::RED . "You moved too much logout was canceled!");
                $this->getHandler()->cancel();
				return;
			}
			if($player->getCooldown()->has('combattag')) {
				$player->setCanLogout(false);
				$player->getCooldown()->remove('logout');
				return;
			}
			if(!$player->getCooldown()->has('logout')){
				$player->setCanLogout(false);
                $this->getHandler()->cancel();
			}
		}
	}
}