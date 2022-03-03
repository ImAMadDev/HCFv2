<?php

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimFlags;
use ImAMadDev\HCF;
use ImAMadDev\manager\{
	ClaimManager,
	PurgeManager
};
use ImAMadDev\player\HCFPlayer;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\ItemIds;
use pocketmine\player\GameMode;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class ClaimListener implements Listener
{

    public function handleMove(PlayerMoveEvent $event) : void
    {
        $player = $event->getPlayer();
        if ($player instanceof HCFPlayer) {
            if (!$event->getFrom()->equals($event->getTo()->asVector3())) $player->checkWall();
            if ($player->hasCancelledMovement()) {
                $player->correctMovement();
            }
            $claim = ClaimManager::getInstance()->getClaimByPosition($player->getPosition());
            if ($claim !== null) {
                if ($claim->join($player) === false) {
                    $event->setTo($event->getFrom());
                    $event->cancel();
                    return;
                }
                if ($claim->getProperties()->getName() !== $player->getRegion()->get()) {
                    $player->getRegion()->set($claim->getProperties()->getName());
                }
            } else {
                if ("Wilderness" !== $player->getRegion()->get()) {
                    if ($player->getGamemode() === GameMode::ADVENTURE()) {
                        $player->setGamemode(GameMode::SURVIVAL());
                        return;
                    }
                    $player->getRegion()->set();
                }
            }
        }
    }


    public function handleInteract(PlayerInteractEvent $event) : void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $claim = ClaimManager::getInstance()->getClaimByPosition($block->getPosition());
        if($player->getGamemode() === GameMode::CREATIVE() && Server::getInstance()->isOp(strtolower($player->getName()))) return;
        if ($player instanceof HCFPlayer) {
            if ($player->getCooldown()->has('antitrapper_tag')) {
                if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if ($claim instanceof NonEditableClaim){
                        $event->cancel();
                        return;
                    }
                    if ($claim instanceof EditableClaim){
                        if($claim->getProperties()->hasFlag(ClaimFlags::INTERACT_CANCEL)){
                            if($claim->getProperties()->getFlag(ClaimFlags::INTERACT_CANCEL)->run($block) == false) {
                                $event->cancel();
                                HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                                    $player->cancelMovement(true);
                                }
                                ), 1);
                                HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                                    $player->cancelMovement(false);
                                }
                                ), 40);
                                return;
                            }
                        }
                        if($claim->getProperties()->hasFlag(ClaimFlags::INTERACT)){
                            if($claim->getProperties()->getFlag(ClaimFlags::INTERACT)->run($block) == false) {
                                $event->cancel();
                                return;
                            }
                        }
                    }
                }
            }
            if ($claim instanceof EditableClaim){
                if(!$claim->canEdit($player->getFaction()) && $claim->getProperties()->hasFlag(ClaimFlags::INTERACT_CANCEL) && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
                    if($claim->getProperties()->getFlag(ClaimFlags::INTERACT_CANCEL)->run($block) == false and !PurgeManager::isEnabled()) {
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
                if (!$claim->canEdit($player->getFaction()) && $claim->getProperties()->hasFlag(ClaimFlags::INTERACT) && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if($claim->getProperties()->getFlag(ClaimFlags::INTERACT)->run($block) == false and !PurgeManager::isEnabled()) {
                        $event->cancel();
                    }
                }
            }
        }
    }

    public function handleBreak(BlockBreakEvent $event) : void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if ($player instanceof HCFPlayer) {
            $claim = ClaimManager::getInstance()->getClaimByPosition($block->getPosition());
            if ($player->getGamemode() === GameMode::CREATIVE() && Server::getInstance()->isOp(strtolower($player->getName()))) return;
            if ($player->getCooldown()->has('antitrapper_tag')) {
                $event->cancel();
                return;
            }
            if ($claim instanceof NonEditableClaim){
                $event->cancel();
            }
            if ($claim instanceof EditableClaim) {
                if (!$claim->canEdit($player->getFaction())) {
                    if($claim->getProperties()->hasFlag(ClaimFlags::BREAK)){
                        if($claim->getProperties()->getFlag(ClaimFlags::BREAK)->run($block) == false) {
                            $event->cancel();
                        }
                    }
                }
            }
            if (!$event->isCancelled()) {
                $player->activateEnchantment($event);
            }
        }
    }

    public function handlePlace(BlockPlaceEvent $event) : void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if ($player instanceof HCFPlayer) {
            $claim = ClaimManager::getInstance()->getClaimByPosition($block->getPosition());
            if ($player->getGamemode() === GameMode::CREATIVE() && Server::getInstance()->isOp(strtolower($player->getName()))) return;
            if ($player->getCooldown()->has('antitrapper_tag')) {
                $event->cancel();
                return;
            }
            if ($claim instanceof NonEditableClaim){
                $event->cancel();
                return;
            }
            if ($claim instanceof EditableClaim) {
                if (!$claim->canEdit($player->getFaction())) {
                    if($claim->getProperties()->hasFlag(ClaimFlags::PLACE)){
                        if($claim->getProperties()->getFlag(ClaimFlags::PLACE)->run($block) == false) {
                            $event->cancel();
                        }
                    }
                }
            }
        }
    }

}