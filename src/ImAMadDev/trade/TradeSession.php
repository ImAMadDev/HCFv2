<?php

namespace ImAMadDev\trade;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\manager\TradeManager;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\transaction\SimpleInvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TradeSession {

    private int $time;

	private ?int $tradeTime = null;
	
	private bool $senderStatus = false;
	
	private bool $receiverStatus = false;
	
	private InvMenu $menu;
	
	private int $key;
	
	public function __construct(
        private HCFPlayer $sender,
        private HCFPlayer $receiver) {
        $sender->getTraderPlayer()->setHasSession(true);
        $receiver->getTraderPlayer()->setHasSession(true);
		$this->time = time();
		$this->menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
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
			function(SimpleInvMenuTransaction $st): InvMenuTransactionResult {
				if($st->getAction()->getSlot() === 4 and $st->getPlayer()->getUniqueId()->equals($this->sender->getUniqueId())) {
					if($st->getItemClicked()->getId() === ItemIds::STAINED_GLASS and $st->getItemClicked()->getMeta() === 14) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 13);
						$item->setCustomName(TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "ACCEPT");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
                        $st->getAction()->getInventory()->setItem(4, $item);
						$this->senderStatus = true;
						return $st->discard();
					}elseif($st->getItemClicked()->getId() === ItemIds::STAINED_GLASS and $st->getItemClicked()->getMeta() === 13) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14);
						$item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
						$st->getAction()->getInventory()->setItem(4, $item);
						$this->senderStatus = false;
						return $st->discard();
					}
				}
				if($st->getAction()->getSlot() === 22 and $st->getPlayer()->getUniqueId()->equals($this->receiver->getUniqueId())) {
					if($st->getItemClicked()->getId() === ItemIds::STAINED_GLASS and $st->getItemClicked()->getMeta() === 14) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 13);
						$item->setCustomName(TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "ACCEPT");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
                        $st->getAction()->getInventory()->setItem(22, $item);
						$this->receiverStatus = true;
						return $st->discard();
					} elseif($st->getItemClicked()->getId() === ItemIds::STAINED_GLASS and $st->getItemClicked()->getMeta() === 13) {
						$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS, 14);
						$item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
						$item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
                        $st->getAction()->getInventory()->setItem(22, $item);
						$this->receiverStatus = false;
						return $st->discard();
					}
				}
				if($st->getAction()->getSlot() === 13) {
					return $st->discard();
				}
				if(($st->getAction()->getSlot() % 9) < 4 and $st->getPlayer()->getUniqueId()->equals($this->sender->getUniqueId()) and $this->senderStatus === false and $this->receiverStatus === false) {
					return $st->continue();
				}
				if(($st->getAction()->getSlot() % 9) > 4 and $st->getPlayer()->getUniqueId()->equals($this->receiver->getUniqueId()) and $this->receiverStatus === false and $this->senderStatus === false) {
					return $st->continue();
				}
				return $st->discard();
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
							$this->sender->getWorld()->dropItem($this->sender->getPosition(), $item);
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
							$this->receiver->getWorld()->dropItem($this->receiver->getPosition(), $item);
						} else {
							$this->sender->sendMessage(TextFormat::RED . "Sender is Offline");
							return; 
						}
					}
				}
				$this->menu->getInventory()->clearAll();
				TradeManager::getInstance()->removeSession($this->key);
			}
		);
        $this->sendMenus();
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
				$this->sender->removeCurrentWindow();
				$this->sender->sendMessage("successTrade");
			}
			if($this->receiver->isOnline()) {
				$this->receiver->removeCurrentWindow();
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
							$this->receiver->getWorld()->dropItem($this->receiver->getPosition(), $item);
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
							$this->receiver->getWorld()->dropItem($this->receiver->getPosition(), $item);
						} else {
							$this->sender->sendMessage(TextFormat::RED . "Sender is Offline");
							return; 
						}
					}
				}$this->menu->getInventory()->clearAll();
                if($this->sender->isOnline()) {
                    $this->sender->removeCurrentWindow();
                    $this->sender->sendMessage("successTrade");
                }
                if($this->receiver->isOnline()) {
                    $this->receiver->removeCurrentWindow();
                    $this->receiver->sendMessage("successTrade");
                }
                $manager->removeSession($key);
            }
        }
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    public function __destruct()
    {
        $this->getSender()?->getTraderPlayer()?->setHasSession(true);
        $this->getReceiver()?->getTraderPlayer()?->setHasSession(true);
    }
}