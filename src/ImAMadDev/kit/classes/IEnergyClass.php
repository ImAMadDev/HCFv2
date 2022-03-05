<?php

namespace ImAMadDev\kit\classes;

use ImAMadDev\player\HCFPlayer;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\effect\EffectInstance;

use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\ability\Ability;
use ImAMadDev\manager\{
	ClaimManager,
	EOTWManager
};
abstract class IEnergyClass extends IClass
{

    abstract public function getMaxEnergy() : float | int;
    
    abstract public function getEnergyCost(Item $item): int | float;
    
    abstract public function isClickItem(Item $item): bool;
    
    abstract public function getEffectPassiveItem(Item $item): ?EffectInstance;
    
    public function itemConsumed(HCFPlayer $player, Item $item): bool
    {
    	if ($item->getNamedTag()->getTag(Ability::ABILITY) instanceof CompoundTag) {
    		$player->sendMessage(TextFormat::RED . "You can't use this item because is an  ability!");
    		return false;
    	}
    	if ($player->getCooldown()->has('effects_cooldown')) {
    		$player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . $this->getName() . " Buff" . TextFormat::RED . " because you have a cooldown of " . gmdate('i:s', $player->getCooldown()->get('effects_cooldown')));
    		return false;
    	}
    	if ($player->isInvincible()) {
    		$player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . $this->getName() . " Buff" . TextFormat::RED . " because your pvp timer is enabled");
            return false;
        }
        if (ClaimManager::getInstance()->getClaimByPosition($player->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN && !EOTWManager::isEnabled()) {
        	$player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . $this->getName() . " Buff" . TextFormat::RED . " because you're in the spawn");
        	return false;
        }
        if ($player->getClassEnergy()->getEnergy() < $this->getEnergyCost($item)) {
        	$player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::GOLD . $this->getName() . " Buff" . TextFormat::RED . " because you don't have enough energy, you need: " . $this->getEnergyCost($item));
        	return false;
        }
        return true;
    }
    
    protected function applyNearbyAllies(HCFPlayer $player, EffectInstance $effect): void
    {
    	foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
    		$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
    		if ($player->getFaction() !== null) {
    			if ($player->getFaction()->isInFaction($nearby) or $player->getFaction()->isAlly($nearbyFaction)) {
    				$nearby->applyPotionEffect($effect);
    			}
    		}
    	}
    }
    
    protected function applyNearbyEnemys(HCFPlayer $player, EffectInstance $effect): void
    {
    	foreach ($player->getNearbyPlayers(40, 40) as $nearby) {
    		$nearbyFaction = $nearby->getFaction() === null ? "" : $nearby->getFaction()->getName();
    		if ($player->getFaction() !== null) {
    			if (!$player->getFaction()->isInFaction($nearby) and !$player->getFaction()->isAlly($nearbyFaction)) {
    				$nearby->applyPotionEffect($effect);
    			}
    		} else {
    			$nearby->applyPotionEffect($effect);
    		}
    	}
    }
}