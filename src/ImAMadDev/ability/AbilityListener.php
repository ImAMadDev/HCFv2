<?php

namespace ImAMadDev\ability;

use ImAMadDev\HCF;
use ImAMadDev\player\{HCFPlayer};
use ImAMadDev\manager\{AbilityManager};

use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerInteractEvent, PlayerItemUseEvent};
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
use pocketmine\nbt\tag\CompoundTag;

class AbilityListener implements Listener {
	
	public function __construct(){
		
	}
	
	public function onPlayerInteractEvent(PlayerItemUseEvent $event) : void {
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		$ability = AbilityManager::getInstance()->getAbilityByItem($item);
		if($ability !== null) {
			$event->cancel();
			if($item->getNamedTag()->getTag(Ability::INTERACT_ABILITY) instanceof CompoundTag && $item->getNamedTag()->getTag(Ability::ABILITY) instanceof CompoundTag) {
                $ability->consume($player, $player->getPosition()->getWorld()->getBlock($player->getPosition()->subtract(0, 1, 0)));
            }
		}
	}
	
	public function onEntityDamageEvent(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($player instanceof HCFPlayer) {
			if($event instanceof EntityDamageByEntityEvent) {
				$damager = $event->getDamager();
				if($damager instanceof HCFPlayer) {
					$item = $damager->getInventory()->getItemInHand();
					$ability = AbilityManager::getInstance()->getAbilityByItem($item);
					if(HCF::getInstance()->getFactionManager()->equalFaction($damager->getFaction(), $player->getFaction()) === false && $event->isCancelled() === false) {
						if($ability !== null) {
							if($item->getNamedTag()->getTag(Ability::DAMAGE_ABILITY) instanceof CompoundTag && $item->getNamedTag()->getTag(Ability::ABILITY) instanceof CompoundTag) {
								$damager->addAbilityHits($item);
								if($damager->canActivateAbility($item) === true) {
									$ability->consume($damager);
								}
							}
						}
					}
				}
			}
		}
	}/*
	
	public function onPlayerItemHeldEvent(PlayerItemHeldEvent $event) : void {
		$player = $event->getPlayer();
		$item = $event->getItem();
		$ability = AbilityManager::getInstance()->getAbilityByItem($item);
		if($ability !== null) {
			$event->setCancelled(true);
			if($item->getNamedTagEntry(Ability::INTERACT_ABILITY) instanceof CompoundTag or $item->getNamedTagEntry(Ability::DAMAGE_ABILITY) instanceof CompoundTag) {
				
			}
		}
	}
	*/
}