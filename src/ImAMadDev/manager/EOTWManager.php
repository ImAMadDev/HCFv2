<?php

namespace ImAMadDev\manager;

use pocketmine\player\GameMode;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerDeathEvent, PlayerRespawnEvent, PlayerPreLoginEvent};
use ImAMadDev\HCF;
use ImAMadDev\ticks\events\EndOfTheWorldTick;

class EOTWManager implements Listener {
	
	public static HCF $main;
	public static bool $enabled = false;
	public static int $time = 3600;
	public static array $died = [];
	public static ?EndOfTheWorldTick $tick = null;
	
	public function __construct(HCF $main) {
		self::$main = $main;
	}
	
	public static function isEnabled() : bool {
		return self::$enabled;
	}
	
	public static function set(bool $status = false, int $time = 60): void {
		self::$enabled = $status;
		self::$time = ($time * 60);
		if($status === true) {
			if(self::$tick === null) {
				self::$tick = new EndOfTheWorldTick(self::$main);
			}
		} else {
			self::$tick->getHandler()->cancel();
			self::$tick = null;
		}
	}
	
	public static function getTime() : int {
		return self::$time;
	}
	
	public static function reduceTime() : void {
		self::$time -= 1;
	}
	
	public function onPlayerPreLoginEvent(PlayerPreLoginEvent $event) : void {
		$player = $event->getPlayerInfo();
		if(self::isEnabled() === true) {
			if(isset(self::$died[$player->getUsername()])) {
				$event->setKickReason(PlayerPreLoginEvent::KICK_REASON_BANNED, TextFormat::RED . "You've died in EOTW! You can't rejoin!");
			}
		}
	}
	
	public function onPlayerDeathEvent(PlayerDeathEvent $event) : void {
		$player = $event->getPlayer();
		if(self::isEnabled() === true) {
			if(!isset(self::$died[$player->getName()])) {
				self::$died[$player->getName()] = $player;
				$player->sendMessage(TextFormat::RED . "You've died in EOTW! You can't respawn!");
			}
		}
	}
	
	public function onPlayerRespawnEvent(PlayerRespawnEvent $event) : void {
		$player = $event->getPlayer();
		if(self::isEnabled() === true) {
			if(isset(self::$died[$player->getName()])) {
				$player->sendMessage(TextFormat::RED . "You've died in EOTW! You can't respawn!"); 
				$player->setGamemode(GameMode::SPECTATOR());
			}
		}
	}
	
}