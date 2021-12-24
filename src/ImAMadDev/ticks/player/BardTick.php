<?php

namespace ImAMadDev\ticks\player;


use ImAMadDev\player\HCFPlayer;
use ImAMadDev\HCF;
use ImAMadDev\manager\{ClaimManager, EOTWManager};

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\ItemIds;
use pocketmine\scheduler\Task;

class BardTick extends Task {

	public function __construct(
        public HCFPlayer $player) {
		HCF::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
	}

	public function onRun() : void{
		$player = $this->player;
		if(!$player->isOnline()){
			$this->getHandler()->cancel();
			return;
		}
        $player->getClassEnergy()->onTick();
		if($player->isBard()) {
			if($player->isInvincible()){
				return;
			}
			if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
				return;
			}
			switch($player->getInventory()->getItemInHand()->getId()) {
				case ItemIds::SUGAR:
					$effect = new EffectInstance(VanillaEffects::SPEED(), 20 * 5, 1, true);
					foreach($player->getNearbyPlayers(40, 40) as $nearby) {
						$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
						if($player->getFaction() !== null) {
							if($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
								$nearby->applyPotionEffect($effect);
							}
						}
					}
					$player->applyPotionEffect($effect);
					break;
				case ItemIds::FEATHER:
					$effect = new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 5, 2, true);
					foreach($player->getNearbyPlayers(40, 40) as $nearby) {
						$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
						if($player->getFaction() !== null) {
							if($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
								$nearby->applyPotionEffect($effect);
							}
						}
					}
					$player->applyPotionEffect($effect);
					break;
				case ItemIds::IRON_INGOT:
					$effect = new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 5, 0, true);
					foreach($player->getNearbyPlayers(40, 40) as $nearby) {
						$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
						if($player->getFaction() !== null) {
							if($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
								$nearby->applyPotionEffect($effect);
							}
						}
					}
					$player->applyPotionEffect($effect);
					break;
				case ItemIds::GHAST_TEAR:
					$effect = new EffectInstance(VanillaEffects::REGENERATION(), 20 * 5, 0, true);
					foreach($player->getNearbyPlayers(40, 40) as $nearby) {
						$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
						if($player->getFaction() !== null) {
							if($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
								$nearby->applyPotionEffect($effect);
							}
						}
					}
					$player->applyPotionEffect($effect);
					break;
				case ItemIds::BLAZE_POWDER:
					$effect = new EffectInstance(VanillaEffects::STRENGTH(), 20 * 5, 0, true);
					foreach($player->getNearbyPlayers(40, 40) as $nearby) {
						$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
						if($player->getFaction() !== null) {
							if($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
								$nearby->applyPotionEffect($effect);
							}
						}
					}
					$player->applyPotionEffect($effect);
					break;
			}
		}
	}
}