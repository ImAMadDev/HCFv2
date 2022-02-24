<?php

namespace ImAMadDev\faction\command\subCommands;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\faction\FactionUtils;
use ImAMadDev\HCF;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\manager\{EOTWManager, ClaimManager};

class CreateSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("create", "/faction create (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(EOTWManager::isEnabled() === true) {
			$sender->sendMessage(TextFormat::RED . "You cannot do this because End Of The World is enabled");
			return;
		}
		if($sender->getFaction() !== null) {
			$sender->sendMessage(TextFormat::RED . "You already have a faction!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		$text = preg_replace('([^A-Za-z0-9 ])', '', TextFormat::clean($args[1]));
		$name = str_replace(" ", "_", $text);
		if(strlen($name) > 20) {
			$sender->sendMessage(TextFormat::RED . "Faction name is too long, maximum characters are 20");
			return;
		}
		if(HCF::$factionManager->isFaction($name)) {
			$sender->sendMessage(TextFormat::RED . "Faction $name already exist");
			return;
		}
		if(ClaimManager::getInstance()->getClaim($name) !== null or $name === "Wilderness" or stripos($name, "Spawn") !== false or stripos($name, "Warzone") !== false or stripos($name, "Road") !== false or stripos($name, "KOTH") !== false){
			$sender->sendMessage(TextFormat::RED . "This name can't be used");
			return;
		}
		if($sender->getBalance() < FactionUtils::FACTION_PRICE) {
			$sender->sendMessage(TextFormat::RED . "You doesn't have enough money");
			return;
		}
		$data = ['name' => $name,
			'leader' => $sender->getName(),
			'coleaders' => [],
			'members' => [],
			'allys' => [],
			'points' => 0,
			'kills' => 0,
			'balance' => FactionUtils::FACTION_PRICE,
			'dtr' => FactionUtils::MINIMUM_DTR,
			'level' => HCFUtils::DEFAULT_MAP,
			'home' => "0:0:0",
			'disqualified' => false,
			'stickes' => 0,
			'x1' => null,
			'z1' => null,
			'x2' => null,
			'z2' => null 
		];
		if(HCF::$factionManager->createFaction($data, $sender)) {
			$this->getServer()->broadcastMessage(TextFormat::DARK_AQUA . "Faction " . TextFormat::GOLD . $name . TextFormat::DARK_AQUA . " was successful created by " . TextFormat::GRAY . $sender->getName(). "!");
		}
    }
}