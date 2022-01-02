<?php

namespace ImAMadDev\kit\command;

use ImAMadDev\command\Command;
use ImAMadDev\kit\command\subCommands\CreateKitSubCommand;
use ImAMadDev\kit\command\subCommands\RemoveSubCommand;
use ImAMadDev\kit\command\subCommands\SeeSubCommand;
use ImAMadDev\kit\command\subCommands\ResetSubCommand;
use ImAMadDev\manager\{EOTWManager, KitManager};
use muqsit\invmenu\type\InvMenuTypeIds;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\kit\Kit;
use ImAMadDev\utils\HCFUtils;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\InvMenuTransaction;
use formapi\SimpleForm;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class KitCommand extends Command {
	
	public function __construct() {
		parent::__construct("kit", "Manage kit", "/kit see (kit name)", ["gkit"]);
		$this->addSubCommand(new SeeSubCommand());
        $this->addSubCommand(new ResetSubCommand());
        $this->addSubCommand(new CreateKitSubCommand());
        $this->addSubCommand(new RemoveSubCommand());
	}
	
	public function execute(CommandSender $sender, string $commandLabel, array $args): void {
		if(isset($args[0])) {
			$subCommand = $this->getSubCommand($args[0]);
			if($subCommand !== null) {
				$subCommand->execute($sender, $commandLabel, $args);
				return;
			}
			$sender->sendMessage(TextFormat::RED . $this->getUsage());
			return;
		}
		if($sender instanceof HCFPlayer) {
			if(isset(EOTWManager::$died[$sender->getName()])) {
				$sender->sendMessage(TextFormat::RED . "You've died in EOTW! You can't respawn!");
				return;
			}
			if($sender->getPlayerInfo()->getExtraData()['CurrentInputMode'] === "Classic") {
				$this->getKits($sender);
			} else {
				$this->getKitsAndroid($sender);
			}
		}
	}
	
	public function getKits(HCFPlayer $player) : void {
		$menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$menu->setName(TextFormat::GREEN . "Kits Menu");
		$menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
			$player = $transaction->getPlayer();
			$kitName = TextFormat::clean($transaction->getItemClicked()->getName());
			$possibleKitName = TextFormat::clean($transaction->getItemClickedWith()->getName());
			if(($kit = KitManager::getInstance()->getKitByName($kitName)) instanceof Kit) {
				if($kit->getPermission() !== "default.permission" and !$player->hasPermission($kit->getPermission())) {
					$player->sendMessage(TextFormat::RED . "You don't have permission to do this!");
				} else {
					$time = ($kit->getCooldown() - (time() - $player->getCache()->getCountdown($kit->getName())));
					if($time > 0) {
                        $player->sendMessage(TextFormat::RED . "You can't use this kit because you have a countdown of: " . HCFUtils::getTimeString($player->getCache()->getCountdown($kit->getName())));
                    } else {
                        $player->getCache()->setCountdown($kit->getName(), $kit->getCooldown());
                        $kit->giveKit($player);
                        $player->removeCurrentWindow();
                    }
				}
			} elseif(($kit = KitManager::getInstance()->getKitByName($possibleKitName)) instanceof Kit) {
				if($kit->getPermission() !== "default.permission" and !$player->hasPermission($kit->getPermission())) {
					$player->sendMessage(TextFormat::RED . "You don't have permission to do this!");
				} else {
					$time = ($kit->getCooldown() - (time() - $player->getCache()->getCountdown($kit->getName())));
					if($time > 0) {
                        $player->sendMessage(TextFormat::RED . "You can't use this kit because you have a countdown of: " . HCFUtils::getTimeString($player->getCache()->getCountdown($kit->getName())));
                    } else {
                        $player->getCache()->setCountdown($kit->getName(), $kit->getCooldown());
                        $kit->giveKit($player);
                        $player->removeCurrentWindow();
                    }
				}
			} else {
				$player->sendMessage(TextFormat::RED . "An unknown error occurred, try again later!");
			}
			return $transaction->discard();
		});
		$menu->send($player);
		foreach(KitManager::getInstance()->getKits() as $kit) {
			$item = $kit->getIcon()->setLore([$kit->getDescription(), "Countdown: " . HCFUtils::getTimeString(time() + $kit->getCooldown()), "Available in: " . HCFUtils::getTimeString($player->getCache()->getCountdown($kit->getName()))]);
			$menu->getInventory()->setItem($kit->getSlot(), $item);
		}
	}
	
	public function getKitsAndroid(Player $player){
		$form = new SimpleForm(function (Player $player, $data) {
			if($data === null){
				return;
			}
			if(($kit = KitManager::getInstance()->getKitByName($data)) instanceof Kit) {
				if($kit->getPermission() !== "default.permission" and !$player->hasPermission($kit->getPermission())) {
					$player->sendMessage(TextFormat::RED . "You don't have permission to do this!");
				} else {
					$time = ($kit->getCooldown() - (time() - $player->getCache()->getCountdown($kit->getName())));
					if($time <= 0) {
                        $player->getCache()->setCountdown($kit->getName(), $kit->getCooldown());
						$kit->giveKit($player);
					} else {
						$player->sendMessage(TextFormat::RED . "You can't use this kit because you have a countdown of: " . HCFUtils::getTimeString($player->getCache()->getCountdown($kit->getName())));
					}
				}
			}
		});
		$form->setTitle(TextFormat::GREEN . "Kits Menu");
		foreach(KitManager::getInstance()->getKits() as $kit) {
			$form->addButton($kit->getIcon()->getCustomName(), -1, "", $kit->getName());
		}
		$form->sendToPlayer($player);
	}
}