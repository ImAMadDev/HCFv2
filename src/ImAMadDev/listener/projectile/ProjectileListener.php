<?php

namespace ImAMadDev\listener\projectile;

use ImAMadDev\entity\projectile\EnderPearl;
use ImAMadDev\entity\projectile\SplashPotion;
use ImAMadDev\item\EnderPearl as PearlItem;
use ImAMadDev\item\SplashPotion as PotionSplash;

use pocketmine\block\{FenceGate, Fence};
use ImAMadDev\utils\NBT;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerInteractEvent;

class ProjectileListener implements Listener {
	
	public function __construct(){
	}
	
	public function onPlayerInteractEvent(PlayerItemUseEvent $event) : void {
		$player = $event->getPlayer();
		$item = $event->getItem();
        /*
		if($item instanceof FishingRod){
			if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
				$nbt = NBT::createWith($player);
				$entity = Entity::createEntity("FishingHook", $player->getWorld(), $nbt, $player);
				$entity->spawnToAll();
				if($entity instanceof FishingHook){
					$entity->setMotion($entity->getMotion()->multiply($item->getThrowForce()));
				}
			}
		}*/
		if($item instanceof PotionSplash){
            $entity = new SplashPotion(NBT::createWith($player), $player, $item->getPotionType());
            $entity->setMotion($player->getDirectionVector()->multiply($item->getThrowForce()));
            $entity->spawnToAll();
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
		}
		if($item instanceof PearlItem){
			if($player->getCooldown()->has('enderpearl')){
				$player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::LIGHT_PURPLE . "enderpearl " . TextFormat::RED . "because you have a cooldown of " .$player->getCooldown()->get('enderpearl'));
				$event->cancel();
				return;
			}
			$entity = new EnderPearl(NBT::createWith($player), $player);
            $entity->setMotion($player->getDirectionVector()->multiply($item->getThrowForce()));
			$entity->spawnToAll();
			$player->getCooldown()->add('enderpearl', 16);
			$item->setCount($item->getCount() - 1);
			$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
		}
        /*
		if($item instanceof PearlItem && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			if($block instanceof Fence||$block instanceof FenceGate){
				$event->cancel();
				if($player->getCooldown()->has('enderpearl')){
                    $player->sendMessage(TextFormat::RED . "You can't use " . TextFormat::LIGHT_PURPLE . "enderpearl " . TextFormat::RED . "because you have a cooldown of " .$player->getCooldown()->get('enderpearl'));
					$event->cancel();
					return;
				}
                $entity = new EnderPearl(NBT::createWith($player), $player);
                $entity->setMotion($player->getDirectionVector()->multiply($item->getThrowForce()));
                $entity->spawnToAll();
				$player->getCooldown()->add('enderpearl', 16);
				$item->setCount($item->getCount() - 1);
				$player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : VanillaItems::AIR());
			}
		}*/
	}
	
}