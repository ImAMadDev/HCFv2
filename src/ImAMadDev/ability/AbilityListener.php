<?php

namespace ImAMadDev\ability;

use ImAMadDev\ability\utils\DamageAbility;
use ImAMadDev\ability\utils\DamageOtherAbility;
use ImAMadDev\ability\utils\InteractionAbility;
use ImAMadDev\ability\utils\InteractionBlockAbility;
use ImAMadDev\HCF;
use ImAMadDev\player\{HCFPlayer};
use ImAMadDev\manager\{AbilityManager};

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerInteractEvent, PlayerItemUseEvent};
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};
use pocketmine\nbt\tag\CompoundTag;

class AbilityListener implements Listener {
	
	public function __construct(){}

    public function handlePlace(BlockPlaceEvent $event) : void
    {
        $item = $event->getItem();
        $ability = AbilityManager::getInstance()->getAbilityByItem($item);
        if ($ability !== null and get_parent_class($ability) !== "InteractionBlockAbility"){
            $event->cancel();
        }
    }
	
	public function handleItemUse(PlayerItemUseEvent $event) : void {
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		$ability = AbilityManager::getInstance()->getAbilityByItem($item);
		if($ability instanceof InteractionAbility) {
			$event->cancel();
			if($item->getNamedTag()->getTag(Ability::INTERACT_ABILITY) instanceof CompoundTag && $item->getNamedTag()->getTag(Ability::ABILITY) instanceof CompoundTag) {
                $ability->consume($player);
            }
		}
	}

    public function handleInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $ability = AbilityManager::getInstance()->getAbilityByItem($item);
        if($ability instanceof InteractionBlockAbility) {
            $event->cancel();
            if($item->getNamedTag()->getTag(Ability::INTERACT_ABILITY) instanceof CompoundTag && $item->getNamedTag()->getTag(Ability::ABILITY) instanceof CompoundTag) {
                $ability->consume($player, $event->getBlock(), $event->getFace());
            }
        }
    }
	
	public function onEntityDamageEvent(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($player instanceof HCFPlayer) {
			if($event instanceof EntityDamageByEntityEvent) {
				$attacker = $event->getDamager();
				if($attacker instanceof HCFPlayer) {
					$item = $attacker->getInventory()->getItemInHand();
					$ability = AbilityManager::getInstance()->getAbilityByItem($item);
					if(HCF::getInstance()->getFactionManager()->equalFaction($attacker->getFaction(), $player->getFaction()) === false && $event->isCancelled() === false) {
						if($ability instanceof DamageOtherAbility) {
							if($item->getNamedTag()->getTag(Ability::DAMAGE_ABILITY) instanceof CompoundTag && $item->getNamedTag()->getTag(Ability::ABILITY) instanceof CompoundTag) {
								$attacker->addAbilityHits($item);
								if($attacker->canActivateAbility($item) === true) {
									$ability->consume($attacker, $player);
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