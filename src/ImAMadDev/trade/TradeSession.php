<?php

namespace ImAMadDev\trade;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\TradeManager;
use muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TradeSession {
	
	private HCFPlayer $sender;
	
	private HCFPlayer $receiver;
	
	private int $time;
	
	private ?int $tradeTime = null;
	
	private bool $senderStatus = false;
	
	private bool $receiverStatus = false;
	
	private InvMenu $menu;
	
	private int $key;
	
	public function __construct(HCFPlayer $sender, HCFPlayer $receiver) {
		$this->sender = $sender;
		$this->receiver = $receiver;
		$this->time = time();
		$this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->menu->setName(TextFormat::YELLOW . "Trading Session");
		$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14);
		$item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
		$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
		$this->menu->getInventory()->setItem(4, $item);
		$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14);
		$item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
		$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
		$this->menu->getInventory()->setItem(22, $item);
		$this->menu->setListener(
			function(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action): bool {
				if($action->getSlot() === 4 and $player->getRawUniqueId() === $this->sender->getRawUniqueId()) {
					if($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 14) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 13);
						$item->setCustomName(TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "ACCEPT");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
						$action->getInventory()->setItem(4, $item);
						$this->senderStatus = true;
						return false;
					}elseif($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 13) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14);
						$item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
						$action->getInventory()->setItem(4, $item);
						$this->senderStatus = false;
						return false;
					}
				}
				if($action->getSlot() === 22 and $player->getRawUniqueId() === $this->receiver->getRawUniqueId()) {
					if($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 14) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 13);
						$item->setCustomName(TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "ACCEPT");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
						$action->getInventory()->setItem(22, $item);
						$this->receiverStatus = true;
						return false;
					} elseif($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 13) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14);
						$item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
						$action->getInventory()->setItem(22, $item);
						$this->receiverStatus = false;
						return false;
					}
				}
				if($action->getSlot() === 13) {
					return false;
				}
				if(($action->getSlot() % 9) < 4 and $player->getRawUniqueId() === $this->sender->getRawUniqueId() and $this->senderStatus === false and $this->receiverStatus === false) {
					return true;
				}
				if(($action->getSlot() % 9) > 4 and $player->getRawUniqueId() === $this->receiver->getRawUniqueId() and $this->receiverStatus === false and $this->senderStatus === false) {
					return true;
				}
				return false;
			}
		);
		$this->menu->setInventoryCloseListener(
			function(Player $player, Inventory $inventory): void {
				if(!$this->sender->isOnline() or !$this->receiver->isOnline()) {
					return;
				}
				foreach($this->menu->getInventory()->getContents() as $slot => $item) {
					if(($slot % 9) < 4) {
						if($this->sender->isOnline()) {
							$inventory = $this->sender->getInventory();
							if($inventory->canAddItem($item)) {
								$inventory->addItem($item);
								continue;
							}
							$this->sender->getWorld()->dropItem($this->sender, $item);
						} else {
							$this->receiver->sendMessage(TextFormat::RED . "Sender is Offline");
							return;
						}
					}
				}
				foreach($this->menu->getInventory()->getContents() as $slot => $item) {
					if(($slot % 9) > 4) {
						if($this->receiver->isOnline()) {
							$inventory = $this->receiver->getInventory();
							if($inventory->canAddItem($item)) {
								$inventory->addItem($item);
								continue;
							}
							$this->receiver->getWorld()->dropItem($this->receiver, $item);
						} else {
							$this->sender->sendMessage(TextFormat::RED . "Sender is Offline");
							return; 
						}
					}
				}
				$this->menu->getInventory()->clearAll(true);
				if($this->sender->isOnline()) {
					$this->sender->removeWindow($this->menu->getInventory(), true);
				}
				if($this->receiver->isOnline()) {
					$this->receiver->removeWindow($this->menu->getInventory(), true);
				}
				TradeManager::getInstance()->removeSession($this->key);
			}
		);
	}
	
	public function getSender(): HCFPlayer {
		return $this->sender;
	}
	
	public function getReceiver(): HCFPlayer {
		return $this->receiver;
	}
	
	public function sendMenus() {
		$this->menu->send($this->sender);
		$this->menu->send($this->receiver);
	}
	
	public function tick(int $key, TradeManager $manager): void {
		$this->key = $key;
		if(!$this->sender->isOnline() or !$this->receiver->isOnline()) {
			if($this->sender->isOnline()) {
				$this->sender->removeWindow($this->menu->getInventory(), true);
				$this->sender->sendMessage("successTrade");
			}
			if($this->receiver->isOnline()) {
				$this->receiver->removeWindow($this->menu->getInventory(), true);
				$this->receiver->sendMessage("successTrade");
			}
			$manager->removeSession($key);
			return;
		}
		if($this->senderStatus === true and $this->receiverStatus === true) {
			if($this->tradeTime === null) {
				$this->tradeTime = time();
			}
		} else {
			$this->tradeTime = null;
		}
		if($this->tradeTime !== null) {
			$time = 5 - (time() - $this->tradeTime);
			$this->menu->getInventory()->setItem(13, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 0, $time)->setCustomName(TextFormat::RESET . TextFormat::GRAY . "Trade in $time seconds"));
			if($time <= 1) {
				foreach($this->menu->getInventory()->getContents() as $slot => $item) {
					if(($slot % 9) < 4) {
						if($this->receiver->isOnline()) {
							$inventory = $this->receiver->getInventory();
							if($inventory->canAddItem($item)) {
								$inventory->addItem($item);
								continue;
							}
							$this->receiver->getWorld()->dropItem($this->receiver, $item);
						} else {
							$this->receiver->sendMessage(TextFormat::RED . "Sender is Offline");
							return; 
						}
					}
				}
				foreach($this->menu->getInventory()->getContents() as $slot => $item) {
					if(($slot % 9) > 4) {
						if($this->receiver->isOnline()) {
							$inventory = $this->receiver->getInventory();
							if($inventory->canAddItem($item)) {
								$inventory->addItem($item);
								continue;
							}
							$this->receiver->getWorld()->dropItem($this->receiver, $item);
						} else {
							$this->sender->sendMessage(TextFormat::RED . "Sender is Offline");
							return; 
						}
					}
				}$this->menu->getInventory()->clearAll(true);
                if($this->sender->isOnline()) {
                    $this->sender->removeWindow($this->menu->getInventory(), true);
                    $this->sender->sendMessage("successTrade");
                }
                if($this->receiver->isOnline()) {
                    $this->receiver->removeWindow($this->menu->getInventory(), true);
                    $this->receiver->sendMessage("successTrade");
                }
                $manager->removeSession($key);
            }
        }
    }
}