<?php

namespace ImAMadDev\faction;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\ticks\ReplaceBlockTick;

use pocketmine\block\BlockLegacyIds;
use pocketmine\event\Listener;
use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\event\block\{BlockPlaceEvent, BlockBreakEvent};
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent};

class FactionListener implements Listener {

	public function onEntityDamageEvent(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($player instanceof HCFPlayer) {
			if($event instanceof EntityDamageByEntityEvent) {
				$damager = $event->getDamager();
				if($damager instanceof HCFPlayer){
					if($player->getFaction() !== null && $damager->getFaction() !== null) {
						if(HCF::getInstance()->getFactionManager()->equalFaction($player->getFaction(), $damager->getFaction())) {
							$event->cancel();
						} else {
							if($player->getFaction()->isAlly($damager->getFaction()->getName())) {
								$event->cancel();
							}
						}
					}
					if($event->getCause() !== EntityDamageEvent::CAUSE_VOID && $event->getCause() !== EntityDamageEvent::CAUSE_FALL) {
						if(!$event->isCancelled()) {
							$damager->activateEnchantment($event);
							$damager->getCooldown()->add('combattag', 30);
							HCF::getInstance()->getCombatManager()->setTagged($damager, $player, true);
							HCF::getInstance()->getCombatManager()->setTagged($player, $damager, true);
						}
					}
				}
			}
			if($event->getCause() !== EntityDamageEvent::CAUSE_VOID && $event->getCause() !== EntityDamageEvent::CAUSE_FALL) {
				if(!$event->isCancelled()) {
					$player->getCooldown()->add('combattag', 30);
				}
			}
		}
	}

    /*
	public function onPlayerInteractEvent(PlayerInteractEvent $event) : void {
		$block = $event->getBlock();
		$player = $event->getPlayer();
		$claim = ClaimManager::getInstance()->getClaimByPosition($block->getPosition());
		if($player->getGamemode() === GameMode::CREATIVE() && Server::getInstance()->isOp(strtolower($player->getName()))) return;
        if ($player instanceof HCFPlayer) {
            if ($player->getCooldown()->has('antitrapper_tag')) {
                if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if ($claim instanceof Claim){
                        if($claim->getProperties()->getFlag(ClaimFlags::INTERACT)?->run($block)){
                            $event->cancel();
                        }
                    }
                    if (in_array($block->getId(), array(3, 58, 61, 62, 54, 205, 218, 145, 146, 116, 130, 154))) {
                        $event->cancel();
                    }
                    if (in_array($block->getId(), array(330, 324, 71, 64, 93, 94, 95, 96, 97, 107, 183, 184, 185, 186, 187, 167)) && $player->getInventory()->getItemInHand()->getId() !== ItemIds::ENDER_PEARL) {
                        $event->cancel();
                        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                            $player->cancelMovement(true);
                        }
                        ), 1);
                        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                            $player->cancelMovement(false);
                        }
                        ), 40);
                    }
                }
            }
            if ($claim !== null) {
                if (!$claim->canEdit($player->getFaction()) && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if (in_array($block->getId(), array(3, 58, 61, 62, 54, 205, 218, 145, 146, 116, 130, 154))) {
                        $event->cancel();
                        return;
                    }
                    if (in_array($block->getId(), array(330, 324, 71, 64, 93, 94, 95, 96, 97, 107, 183, 184, 185, 186, 187, 167)) && $player->getInventory()->getItemInHand()->getId() !== ItemIds::ENDER_PEARL) {
                        $event->cancel();
                        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                            $player->cancelMovement(true);
                        }
                        ), 1);
                        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                            $player->cancelMovement(false);
                        }
                        ), 40);
                    }
                } else {
                    if ($player->getCooldown()->has('antitrapper_tag')) {
                        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                            if (in_array($block->getId(), array(3, 58, 61, 62, 54, 205, 218, 145, 146, 116, 130, 154))) {
                                $event->cancel();
                            }
                            if (in_array($block->getId(), array(330, 324, 71, 64, 93, 94, 95, 96, 97, 107, 183, 184, 185, 186, 187, 167)) && $player->getInventory()->getItemInHand()->getId() !== ItemIds::ENDER_PEARL) {
                                $event->cancel();
                                HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                                    $player->cancelMovement(true);
                                }
                                ), 1);
                                HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                                    $player->cancelMovement(false);
                                }
                                ), 40);
                            }
                        }
                    }
                }
            }
        }
	}*/
	
	public function onAntiDrop(BlockBreakEvent $event): void {
		if($event->isCancelled()) return;
		if(($event->getPlayer()->getGamemode() === GameMode::CREATIVE()) && ($event->getPlayer()->hasPermission("edit.lobby"))) return;
		$player = $event->getPlayer();
		$player->getXpManager()->addXp($event->getXpDropAmount());
		$event->setXpDropAmount(0);
		$drops = $event->getDrops();
		foreach($drops as $drop){
			if(!$player->getInventory()->canAddItem($drop)){
				$event->getPlayer()->sendTip(TextFormat::RED . "Your inventory is full, the items will be eliminated");
				break;
			}
			$player->getInventory()->addItem($drop);
		}
		$event->setDrops([]);
	}
	
	public function onCobwerPlace(BlockPlaceEvent $event) : void {
		$block = $event->getBlock();
		$player = $event->getPlayer();
		if($block->getId() === BlockLegacyIds::COBWEB) {
			if($event->isCancelled()) return;
			if(($player->getGamemode() === GameMode::CREATIVE()) && ($player->hasPermission("edit.lobby"))) return;
			HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ReplaceBlockTick(HCF::getInstance(), $block, $block->getPosition()->getWorld()), (20 * 20));
		}
		if($player->getCooldown()->has('deleteblock')) {
			HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ReplaceBlockTick(HCF::getInstance(), $block, $block->getPosition()->getWorld()), (20 * 3));
		}
	}

}