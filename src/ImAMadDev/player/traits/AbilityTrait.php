<?php

namespace ImAMadDev\player\traits;

use pocketmine\item\Item;

use ImAMadDev\ability\utils\DamageOtherAbility;
use ImAMadDev\manager\AbilityManager;

trait AbilityTrait
{
	public array $abilityHits = [];
	
	public array $abilityLastHit = [];
	
	public function canActivateAbility(Item $item) : bool {
        $ability = AbilityManager::getInstance()->getAbilityByItem($item);
		if($ability instanceof DamageOtherAbility) {
			if($ability->getHits() === 0) return true;
			$currentHits = isset($this->abilityHits[$ability->getName()]) ? $this->abilityHits[$ability->getName()] : 0;
			if($currentHits >= $ability->getHits()) {
				return true;
			}
		}
		return false;
	}
	
	public function addAbilityHits(Item $item) : void {
        $ability = AbilityManager::getInstance()->getAbilityByItem($item);
		if($ability instanceof DamageOtherAbility) {
			if(isset($this->abilityHits[$ability->getName()])) {
				$this->abilityHits[$ability->getName()] += 1;
			} else {
				$this->abilityHits[$ability->getName()] = 1;
			}
			$this->abilityLastHit[$ability->getName()] = (time() + 1);
		}
	}
	
	public function checkAbilityLastHit() : void {
		foreach(array_keys($this->abilityHits) as $abi) {
			$remaining = (2 - (time() - $this->abilityLastHit[$abi]));
			if($remaining < 0) {
				$this->abilityHits[$abi] = 0;
				$this->abilityLastHit[$abi] = 0;
			}
		}
	}
}