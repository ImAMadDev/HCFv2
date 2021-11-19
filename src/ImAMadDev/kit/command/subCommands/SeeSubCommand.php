<?php

namespace ImAMadDev\kit\command\subCommands;

use JetBrains\PhpStorm\Pure;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use ImAMadDev\command\SubCommand;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\KitManager;
use ImAMadDev\kit\Kit;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;

class SeeSubCommand extends SubCommand {
	
	#[Pure] public function __construct() {
		parent::__construct("see", "/kit see (string: name)");
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(!$sender instanceof HCFPlayer) {
			$sender->sendMessage(TextFormat::RED . "You must be a player to do this!");
			return;
		}
		if(!isset($args[1])) {
			$sender->sendMessage($this->getUsage());
			return;
		}
		if(($kit = KitManager::getInstance()->getKitByName($args[1])) instanceof Kit) {
			$this->getContents($sender, $kit->getName());
		} else {
			$sender->sendMessage(TextFormat::RED . "Error this kit doesn't exist!");
        }
    }
    
    public function getContents(HCFPlayer $player, string $kitName) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$menu->setName(TextFormat::GREEN . "$kitName Kit Content");
		$menu->setListener(function(InvMenuTransaction $transaction) use($kitName) : InvMenuTransactionResult{
			return $transaction->discard();
		});
		$menu->send($player);
		if(($kit = KitManager::getInstance()->getKitByName($kitName)) instanceof Kit) {
			foreach($kit->getArmor() as $armor) {
				$menu->getInventory()->addItem($armor);
			}
			foreach($kit->getItems() as $item) {
				$menu->getInventory()->addItem($item);
			}
		}
	} 

}