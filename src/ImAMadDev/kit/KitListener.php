<?php

namespace ImAMadDev\kit;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\{EOTWManager, ClaimManager, KitManager};
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\ability\Ability;

use pocketmine\event\player\PlayerChatEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\Server;
use pocketmine\entity\{effect\EffectInstance, effect\VanillaEffects};
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

class KitListener implements Listener {
	
	public function onEntityDamageEventRogue(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($event instanceof EntityDamageByEntityEvent && !$event->isCancelled()){
            $attacker = $event->getDamager();
			if($player instanceof HCFPlayer && $attacker instanceof HCFPlayer){
				if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($attacker->getPosition()), "Spawn") !== false or stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false or $player->isInvincible() or $attacker->isInvincible()) {
					return;
				}
				if($attacker->isRogue() && $attacker->getInventory()->getItemInHand()->getId() === ItemIds::GOLD_SWORD){
					if($attacker->getCooldown()->has('backstab')) {
                        $attacker->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Rogue BackStab" . TextFormat::RED . " because you have a countdown of " . gmdate('i:s', $player->getCooldown()->get('backstab')));
						return;
					}
                    if ($attacker->getDirectionVector()->dot($player->getDirectionVector()) > 0) {
                        $player->setHealth($player->getHealth() / 2);
                        $attacker->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 5 * 20, 3));
                        $attacker->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 5 * 20, 3));
                        $player->sendTitle(TextFormat::RED . TextFormat::BOLD . "Backstabbed!");
                        $attacker->getCooldown()->add('backstab', 15);
                        $attacker->getInventory()->setItemInHand(ItemFactory::air());
                        $attacker->sendMessage(TextFormat::GRAY . "You hit " . TextFormat::RED . $player->getName() . TextFormat::GRAY . " who is now at " . TextFormat::RED . round($player->getHealth()) . " health.");
                    } else {
                        $attacker->sendMessage(TextFormat::RED . "BackStab failed!");
                    }
				}
			}
		}
	}
	
	public function onEntityDamageEventArcher(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($event->isCancelled()) {
			return;
		}
		if($event instanceof EntityDamageByEntityEvent){
			$attacker = $event->getDamager();
			if($player instanceof HCFPlayer and $attacker instanceof HCFPlayer){
				if($event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE) {
					if($attacker->isArcher() && !$player->isArcher()) {
						if($attacker->getInventory()->getItemInHand()->getId() === ItemIds::BOW) {
							if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($attacker->getPosition()), "Spawn") !== false or stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false or $player->isInvincible() or $attacker->isInvincible()) {
								return;
							}
							$player->getArcherMark()->setDistance($attacker->getPosition()->distance($player->getPosition()));
							if($player->getEffects()->has(VanillaEffects::INVISIBILITY())) {
								$player->getEffects()->remove(VanillaEffects::INVISIBILITY());
							}
							if($player->isInvisible()) {
								$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::INVISIBLE, false);
							}
							foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
								$onlinePlayer->showPlayer($player);
							}
                            $attacker->sendMessage(TextFormat::YELLOW . "You have hit " . TextFormat::AQUA . $player->getName() . TextFormat::YELLOW . " and have archer tagged");
                            $player->sendMessage(TextFormat::YELLOW . "You have been archer tagged by " . TextFormat::AQUA . $attacker->getName());
                            $player->sendMessage(TextFormat::YELLOW .  "You marked " . TextFormat::GOLD . $attacker->getName() . " for 10 seconds " . TextFormat::GRAY . "[Damage: " . TextFormat::RED . $event->getBaseDamage() . TextFormat::GRAY . "]");
						}
					}
				}
				if($player->getArcherMark()->getDamage() > 0){
					$baseDamage = $event->getBaseDamage();
					$event->setBaseDamage($baseDamage + $player->getArcherMark()->getDamage());
				}
			}
		}
	}
	
	public function onPlayerInteractEventBard(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
        if ($player instanceof HCFPlayer) {
            if ($player->isBard()) {
                if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") == false && !$player->isInvisible()) {
                    switch ($item->getId()) {
                        case ItemIds::SUGAR:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== "false") {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::DYE:
                            if ($item->getMeta() == 0) {
                                if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                    $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                    return;
                                }
                                if ($player->getCooldown()->has('effects_cooldown')) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                    return;
                                }
                                if ($player->isInvincible()) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                    return;
                                }
                                if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                    return;
                                }
                                if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                    return;
                                }
                                $effect = new EffectInstance(VanillaEffects::INVISIBILITY(), 30 * 10, 0);
                                foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                    $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                    if ($player->getFaction() !== null) {
                                        if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                            $nearby->applyPotionEffect($effect);
                                        }
                                    }
                                }
                                $player->applyPotionEffect($effect);
                                $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                                $item->setCount($item->getCount() - 1);
                                $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                                $player->getCooldown()->add('effects_cooldown', 10);
                                $player->getCooldown()->add('combattag', 30);
                            }
                            break;
                        case ItemIds::FEATHER:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 15, 6);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::GHAST_TEAR:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::REGENERATION(), 20 * 10, 2);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::BLAZE_POWDER:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::STRENGTH(), 20 * 10, 1);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::IRON_INGOT:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 10, 3);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::SPIDER_EYE:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::WITHER(), 20 * 10, 1);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if (!$player->getFaction()->isInFaction($nearby) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                } else {
                                    $nearby->applyPotionEffect($effect);
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::MAGMA_CREAM:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getBardEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Bard Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getBardEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 50 * 50, 1);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                    }
                }
            }
        }
	}
	
	public function onPlayerInteractEventMage(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
        if ($player instanceof HCFPlayer) {
            if ($player->isMage()) {
                if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") == false && !$player->isInvincible()) {
                    switch ($item->getId()) {
                        case ItemIds::COAL:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getMageEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getMageEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::WEAKNESS(), 20 * 15, 0);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if (!$player->getFaction()->isInFaction($nearby) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                } else {
                                    $nearby->applyPotionEffect($effect);
                                }
                            }
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::DYE:
                            if ($item->getMeta() == 2) {
                                if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                    $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                    return;
                                }
                                if ($player->getCooldown()->has('effects_cooldown')) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                    return;
                                }
                                if ($player->isInvincible()) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                    return;
                                }
                                if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you're in the spawn");
                                    return;
                                }
                                if ($player->getClassEnergy()->getEnergy() < $player->getMageEnergyCost($item->getId())) {
                                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getMageEnergyCost($item->getId()));
                                    return;
                                }
                                $effect = new EffectInstance(VanillaEffects::POISON(), 20 * 15, 0);
                                foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                    $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                    if ($player->getFaction() !== null) {
                                        if (!$player->getFaction()->isInFaction($nearby) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                            $nearby->applyPotionEffect($effect);
                                        }
                                    } else {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                }
                                $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                                $item->setCount($item->getCount() - 1);
                                $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                                $player->getCooldown()->add('effects_cooldown', 10);
                                $player->getCooldown()->add('combattag', 30);
                            }
                            break;
                        case ItemIds::ROTTEN_FLESH:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getMageEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getMageEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::HUNGER(), 20 * 15, 0);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if (!$player->getFaction()->isInFaction($nearby) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                } else {
                                    $nearby->applyPotionEffect($effect);
                                }
                            }
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::SPIDER_EYE:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getMageEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getMageEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::WITHER(), 20 * 10, 1);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if (!$player->getFaction()->isInFaction($nearby) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                } else {
                                    $nearby->applyPotionEffect($effect);
                                }
                            }
                            $player->applyPotionEffect($effect);
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::GOLDEN_NUGGET:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getMageEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getMageEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 15, 1);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if (!$player->getFaction()->isInFaction($nearby) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                } else {
                                    $nearby->applyPotionEffect($effect);
                                }
                            }
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                        case ItemIds::SEEDS:
                            if ($item->getNamedTag()->getString(Ability::ABILITY, "false") !== 'false') {
                                $player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
                                return;
                            }
                            if ($player->getCooldown()->has('effects_cooldown')) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
                                return;
                            }
                            if ($player->isInvincible()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because your pvp timer is enabled");
                                return;
                            }
                            if (stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you're in the spawn");
                                return;
                            }
                            if ($player->getClassEnergy()->getEnergy() < $player->getMageEnergyCost($item->getId())) {
                                $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . "Mage Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $player->getMageEnergyCost($item->getId()));
                                return;
                            }
                            $effect = new EffectInstance(VanillaEffects::NAUSEA(), 20 * 15, 0);
                            foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
                                $nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
                                if ($player->getFaction() !== null) {
                                    if (!$player->getFaction()->isInFaction($nearby->getName()) && !$player->getFaction()->isAlly($nearbyFaction)) {
                                        $nearby->applyPotionEffect($effect);
                                    }
                                } else {
                                    $nearby->applyPotionEffect($effect);
                                }
                            }
                            $player->getClassEnergy()->reduce($player->getBardEnergyCost($item->getId()));
                            $item->setCount($item->getCount() - 1);
                            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::air());
                            $player->getCooldown()->add('effects_cooldown', 10);
                            $player->getCooldown()->add('combattag', 30);
                            break;
                    }
                }
            }
        }
	}

    /** @noinspection PhpParamsInspection */
    public function onChat(PlayerChatEvent $event) : void
    {
        $player = $event->getPlayer();
        if (KitManager::getInstance()->hasSession($player)){
            $message = $event->getMessage();
            $args = explode(" ", $message);
            if($args[0] == "help"){
                $player->sendMessage(TextFormat::DARK_AQUA . "Commands: " . PHP_EOL .
                    "- permission (string: permiso del kit)" . PHP_EOL .
                    "- inventory [Todo tu inventario sera escogido para este kit]" . PHP_EOL .
                    "- description (string: la descripcion que aparecera en el kit)" . PHP_EOL .
                    "- icon [el item en tu mano sera el icono del kit]" . PHP_EOL .
                    "- countdown (string: tiempo de refresco del kit) [ejemplo: 1d,2h,30m se lee como 1 dia 2 hora y 30 minutos]" . PHP_EOL .
                    "- slot (int: slot) [esto es para los win10 en que slot del cofre aparecera el icono del kit]" . PHP_EOL .
                    "- customname (string: name) [Aqui podras colocar un nombre customizado para el icono del kit]" . PHP_EOL .
                    "- cancel [cancelar la sesion]" . PHP_EOL .
                    "- save [guardar el kit]"
                );
                $event->cancel();
            }
            if ($args[0] == "permission"){
                if (isset($args[1])){
                    KitManager::getInstance()->getSession($player)->setPermission($args[1]);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit permission to: $args[1]");
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the permission");
                }
                $event->cancel();
            }
            if ($args[0] == "inventory"){
                KitManager::getInstance()->getSession($player)->copyInventory();
                $player->sendMessage(TextFormat::GREEN . "You have save the kit contents");
                $event->cancel();
            }
            if ($args[0] == "description"){
                if (isset($args[1])){
                    $desc = $args;
                    array_shift($desc);
                    $description = implode(" ", $desc);
                    KitManager::getInstance()->getSession($player)->setDescription($description);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit description to: $description");
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the description");
                }
                $event->cancel();
            }
            if ($args[0] == "icon"){
                KitManager::getInstance()->getSession($player)->setIcon();
                $player->sendMessage(TextFormat::GREEN . "You have save the kit icon");
                $event->cancel();
            }
            if ($args[0] == "countdown"){
                if (isset($args[1])){
                    if(!preg_match("/(^[1-9][0-9]{0,2}[mhd])(,[1-9][0-9]{0,2}[mhd]){0,2}$/", $args[1])) {
                        $player->sendMessage(TextFormat::RED . "Unknown countdown type {$args[1]}");
                        return;
                    }
                    $time = HCFUtils::strToSeconds($args[1]);
                    $timeFormated = (time() + $time);
                    KitManager::getInstance()->getSession($player)->setCountdown((int)$time);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit countdown to: " . HCFUtils::getTimeString($timeFormated));
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the permission");
                }
                $event->cancel();
            }
            if ($args[0] == "slot"){
                if (isset($args[1])){
                    KitManager::getInstance()->getSession($player)->setSlot((int)$args[1]);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit slot to: " . (int)$args[1]);
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the slot");
                }
                $event->cancel();
            }
            if ($args[0] == "customname"){
                if (isset($args[1])){
                    $name = $args;
                    array_shift($name);
                    $name = implode(" ", $name);
                    KitManager::getInstance()->getSession($player)->setCustomName($name);
                    $player->sendMessage(TextFormat::GREEN . "You have put the kit icon custom name to: " . $name);
                } else {
                    $player->sendMessage(TextFormat::RED . "Error: please input the icon custom name");
                }
                $event->cancel();
            }
            if ($args[0] == "cancel"){
                KitManager::getInstance()->closeSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have closed your kit creator session");
                $event->cancel();
            }
            if($args[0] == "save"){
                KitManager::getInstance()->getSession($player)->save();
                KitManager::getInstance()->closeSession($player);
                $player->sendMessage(TextFormat::GREEN . "You have save the kit");
                $event->cancel();
            }
        }
	}

}