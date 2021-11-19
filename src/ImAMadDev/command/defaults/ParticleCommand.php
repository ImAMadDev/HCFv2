<?php

namespace ImAMadDev\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\Command;
use ImAMadDev\ticks\player\ParticleTick;
use ImAMadDev\player\HCFPlayer;

class ParticleCommand extends Command {
	
	public function __construct() {
		parent::__construct("particle", "End of the world manager.", "/particle [on - off]");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender->hasPermission('particle.command')) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have permissions to do this!");
			return;
		}
		if(!isset($args[0])) {
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($args[0] == "on"){
			if($sender->hasParticle()) {
				$sender->sendMessage(TextFormat::RED . "You already enabled your particles!");
				return;
			}
			$sender->setParticle(true);
			if($sender->particleTick === null) {
				$sender->particleTick = new ParticleTick($sender);
			}
			$sender->sendMessage(TextFormat::GREEN . "You have enabled your particles!");
		}
		if($args[0] == "off"){
			if($sender->hasParticle() === false) {
				$sender->sendMessage(TextFormat::RED . "You already disabed your particles!");
				return;
			}
			$sender->setParticle(false);
			$sender->sendMessage(TextFormat::GREEN . "You have disabled your particles!");
		}
	}
}