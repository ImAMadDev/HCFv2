<?php

namespace ImAMadDev\listener;

use ImAMadDev\customenchants\CustomEnchantments;
use ImAMadDev\customenchants\types\Gappler;
use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;
use ImAMadDev\player\{HCFPlayer, PlayerUtils, PlayerData};
use ImAMadDev\npc\NPCEntity;
use ImAMadDev\ticks\player\Scoreboard;
use ImAMadDev\manager\{EOTWManager, ClaimManager, SOTWManager};
use ImAMadDev\entity\CombatLogger;
use ImAMadDev\faction\Faction;
use ImAMadDev\utils\{InventoryUtils, HCFUtils};
use ImAMadDev\customenchants\CustomEnchantment;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\{PlayerJoinEvent,
    PlayerKickEvent,
    PlayerPreLoginEvent,
    PlayerRespawnEvent,
    PlayerQuitEvent,
    PlayerCreationEvent,
    PlayerDeathEvent,
    PlayerItemConsumeEvent,
    PlayerInteractEvent,
    PlayerChatEvent};
use pocketmine\event\entity\{EntityDamageEvent,
    EntityItemPickupEvent,
    ItemSpawnEvent,
    EntityTeleportEvent,
    EntityDamageByEntityEvent};
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\PlayerInventory;
use pocketmine\event\inventory\{CraftItemEvent,
    InventoryCloseEvent,
    InventoryTransactionEvent};
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\{InventoryContentPacket,
    InventorySlotPacket,
    InventoryTransactionPacket,
    types\BlockPosition,
    types\inventory\ItemStackWrapper,
    types\inventory\UseItemOnEntityTransactionData,
    UpdateBlockPacket,
    LoginPacket};
use pocketmine\world\sound\ExplodeSound;

class HCFListener implements Listener {

    /*
    public function onPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        if($packet instanceof LoginPacket) {
            foreach ( Server::getInstance()->getNetwork()->getInterfaces() as $interface ) {
                if ( $interface instanceof RakLibInterface ) {
                    try {
                        $reflector = new ReflectionProperty( $interface, "interface" );
                        $reflector->setAccessible( true );
                        $reflector->getValue( $interface )->sendOption( "packetLimit", 900000000000 );
                    } catch ( ReflectionException $e ) {}
                }
            }
            if(isset($packet->clientData["Waterdog_IP"])) {
                $class = new ReflectionClass($event->getOrigin()->getPlayer());

                $prop = $class->getProperty("ip");
                $prop->setAccessible(true);
                $prop->setValue($event->getOrigin()->getPlayer(), $packet->clientData["Waterdog_IP"]);
            }
            if (isset($packet->clientData["Waterdog_XUID"])) {
                $class = new ReflectionClass($event->getOrigin()->getPlayer());

                $prop = $class->getProperty("xuid");
                $prop->setAccessible(true);
                $prop->setValue($event->getOrigin()->getPlayer(), $packet->clientData["Waterdog_XUID"]);
                $packet->xuid = $packet->clientData["Waterdog_XUID"];
            }
        }
    }

    public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void
    {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if ($packet instanceof InventoryTransactionPacket) {
            if ($packet->trData instanceof UseItemOnEntityTransactionData) {
                $entity = $player->getWorld()->getEntity($packet->trData->getActorRuntimeId());
                if ($entity instanceof NPCEntity and $packet->trData->getActionType() == UseItemOnEntityTransactionData::ACTION_INTERACT) {
                    $entity->onActivate($player, $player->getInventory()->getItemInHand());
                }
            }
        }
    }

    public function onDataPacketSend(DataPacketSendEvent $event): void
    {
        $packets = $event->getPackets();
        foreach ($packets as $packet) {
            if ($packet instanceof InventorySlotPacket) {
                $packet->item = new ItemStackWrapper($packet->item->getStackId(), CustomEnchantments::displayEnchants($packet->item->getItemStack()));
            }
            if ($packet instanceof InventoryContentPacket) {
                foreach ($packet->items as $i => $item) {
                    $packet->items[$i] = new ItemStackWrapper($item->getStackId(), CustomEnchantments::displayEnchants($item->getItemStack()));
                }
            }
        }
    }*/


    public function onPickup(EntityItemPickupEvent $event) : void {
		if(($player = $event->getOrigin()) instanceof HCFPlayer){
			if($player->isInvincible() or !$player->isAlive()){
				$event->cancel();
			}
		}
	}

	public function onItemSpawnEvent(ItemSpawnEvent $event) : void {
		$entity = $event->getEntity();
		if(!$entity instanceof ItemEntity) return;
		if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($entity->getPosition()->asPosition()), "Spawn") !== false and SOTWManager::isEnabled()) {
            $player = $entity->getOwningEntity();
            if ($player instanceof HCFPlayer){
                $player->sendMessage(TextFormat::RED . "In the SOTW items dropped in the spawn will be removed automatically.");
            }
			$entity->flagForDespawn();
		}
	}
	
	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
	    if (!$event->getOrigin()->getPlayer() instanceof HCFPlayer) return;
		if(($packet = $event->getPacket()) instanceof LoginPacket) {
			$devices = array(1 => 'Android', 'iOS', 'Mac', 'FireOS', 'GearVR', 'HoloLens', 'Win10', 'Windows', 'Dedicated', 'tvOS', 'PS4', 'Switch', 'Xbox', 'WindowsPhone'); 
			$controls = ["Keyboard", "Touch", "Controller"];
			$input = ["Classic", "Pocket"];
			$dev = $devices[$packet->clientData['DeviceOS']] ?? 'Unknown';
			$control = $controls[$packet->clientData["CurrentInputMode"]] ?? 'Unknown';
			$ui = $input[$packet->clientData["UIProfile"]] ?? 'Classic';
			$event->getOrigin()->getPlayer()->setDeviceString($dev);
			$event->getOrigin()->getPlayer()->setInputString($control);
			$event->getOrigin()->getPlayer()->setUIString($ui);
		}
	}
	
	public function onCreationEvent(PlayerCreationEvent $event) : void {
		$event->setPlayerClass(HCFPlayer::class);
	}
	
	public function onEntityDamageEvent(EntityDamageEvent $event) : void {
		$player = $event->getEntity();
		if($player instanceof HCFPlayer) {
			if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false or $player->isInvincible()) {
				if(EOTWManager::isEnabled() === false) {
					$event->cancel();
				}
			}
			if($event instanceof EntityDamageByEntityEvent) {
				$damager = $event->getDamager();
				if($damager instanceof HCFPlayer){
					if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($damager->getPosition()), "Spawn") !== false or $player->isInvincible() or $damager->isInvincible()) {
						if(EOTWManager::isEnabled() === false) {
							$event->cancel();
						}
					}
				}
			}
		}
	}
	
	public function onJoinEvent(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();
		if (!$player instanceof HCFPlayer) return;
		PlayerData::register($player->getName());
		$event->setJoinMessage(TextFormat::GRAY . "[" . TextFormat::GREEN . "+" . TextFormat::GRAY . "] " . TextFormat::GREEN . $player->getName());
		HCF::getInstance()->getScheduler()->scheduleRepeatingTask(new Scoreboard($player), 20);
		$player->showCoordinates();
		$player->load();
		if(!$player->hasPlayedBefore()) {
			$player->setInvincible();
			HCF::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "rank give " . $player->getName() . " Anubis 3h");
			$player->sendMessage(TextFormat::colorize("&7Welcome to &3MineStalia &c&lBETA 2.0.\n&eYou have received the &5[Anubis] &r&erank for &c3 hours&e."));
            HCFUtils::firstJoin($player);
		}
		$player->setInvincible(PlayerData::getInvincibilityTime($player->getName()));
        $player->getFaction()?->message(TextFormat::GREEN . "+ Member online: " . TextFormat::RED . $player->getName());
		HCFUtils::saveSkin($player->getSkin(), $player->getName());
		$player->setJoined(true);
	}
	
	public function onQuitEvent(PlayerQuitEvent $event) : void {
		$player = $event->getPlayer();
		$event->setQuitMessage(TextFormat::GRAY."[".TextFormat::RED."-".TextFormat::GRAY."] ".TextFormat::RED . $player->getName());
		if($player->canLogout() == true) {
			return;
		}
		if(!$player->isAlive()) {
			return;
		}
		if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && !EOTWManager::isEnabled()) {
			return;
		}
		if($event->getQuitReason() === "Server Closed" || $event->getQuitReason() === "Internal server error"){
			return;
		}
		if($player->getCooldown()->has('combattag')) {
			$player->kill();
			return;
		}
		$time = $player->hasPermission("vip.combatlogger") === true ? 400 : 550;
		$faction = $player->getFaction() === null ? "" : $player->getFaction()->getName();
		$nbt = new CompoundTag();
		$nbt->setString("player", $player->getName());
		$nbt->setString("faction", $faction);
		$entity = new CombatLogger($player->getLocation(), $nbt);
		$entity->setNameTag("CombatLogger");
		$entity->load($time);
		$entity->setHealth(100.0);
		$entity->setMaxHealth(100);
		$entity->spawnToAll();
	}
	
	public function onLevelChangeEvent(EntityTeleportEvent $event) : void {
		$player = $event->getEntity();
		if($player instanceof HCFPlayer and $event->getFrom()->getWorld()->getFolderName() !== $event->getTo()->getWorld()->getFolderName()) {
			$player->showCoordinates();
		}
	}
	
	public function onChatEvent(PlayerChatEvent $event) : void {
		$player = $event->getPlayer();
		$format = null;
		switch($player->getChatMode()) {
			case PlayerUtils::PUBLIC:
				$event->setFormat($player->getChatFormat() . $player->getName() . $player->getCurrentTagFormat() . ': ' . $event->getMessage());
				break;
			case PlayerUtils::FACTION:
				if($player->getFaction() !== null) {
					$event->cancel();
					$player->getFaction()->message(TextFormat::DARK_GREEN . "[Faction] " . $player->getName() . ": " . TextFormat::GOLD . $event->getMessage());
				} else {
					$event->setFormat($player->getChatFormat() . $player->getName() . $player->getCurrentTagFormat() . ': ' . $event->getMessage());
				}
				break;
			case PlayerUtils::ALLY:
				if($player->getFaction() !== null) {
					$event->cancel();
					foreach($player->getFaction()->getAllies() as $ally) {
						if(($allyClass = HCF::getInstance()->getFactionManager()->getFaction($ally)) instanceof Faction) {
							$allyClass->message(TextFormat::DARK_GREEN . "[" . $player->getFaction()->getName() . "] [ALLY] " . $player->getName() . ": " . TextFormat::GOLD . $event->getMessage());
						}
					}
					$player->getFaction()->message(TextFormat::DARK_GREEN . "[ALLY] " . $player->getName() . ": " . TextFormat::GOLD . $event->getMessage());
				} else {
					$event->setFormat($player->getChatFormat() . $player->getName() . $player->getCurrentTagFormat() . ': ' . $event->getMessage());
				}
				break;
			case PlayerUtils::STAFF:
				break;
		}
	}

    public function onPlayerInteractEvent(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$item = $event->getItem();
		if($player->isClaiming()) {
			if($item->getId() !== ItemIds::WOODEN_AXE) {
				return;
			}
			if($player->getFaction() === null or !$player->getFaction()->isLeader($player->getName())) {
				$player->setClaiming(false);
				$player->setFirstClaimingPosition();
				$player->setSecondClaimingPosition();
				$player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
				return;
			}
			if(abs($player->getPosition()->getX()) <= 500 and abs($player->getPosition()->getZ()) <= 500) {
				$player->sendMessage(TextFormat::RED . "You can only claim when you are 300 blocks from spawn!");
				return;
			}
			if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
				if(ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
					$player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
					return;
				}
				if($player->isSneaking()) {
					return;
				}
				$player->setFirstClaimingPosition($block->getPosition());
				$player->sendMessage(TextFormat::GRAY . "You've successfully the claim's " . TextFormat::GREEN . "first" . TextFormat::GRAY . " position!");
			} elseif($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
				if(ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
					$player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
					return;
				} 
				if($player->isSneaking()) {
					return;
				}
				$player->setSecondClaimingPosition($block->getPosition());
				$player->sendMessage(TextFormat::GRAY . "You've successfully the claim's " . TextFormat::GREEN . "second" . TextFormat::GRAY . " position!");
			}
			if($player->getFirstClaimPosition() !== null and $player->getSecondClaimPosition() !== null) {
				$firstPosition = $player->getFirstClaimPosition();
				$firstX = $firstPosition->getX();
				$firstZ = $firstPosition->getZ();
				$secondPosition = $player->getSecondClaimPosition();
				$secondX = $secondPosition->getX();
				$secondZ = $secondPosition->getZ();
				$length = max($firstX, $secondX) - min($firstX, $secondX);
				$width = max($firstZ, $secondZ) - min($firstZ, $secondZ);
				if($length <= 5 or $width <= 5) {
					$player->sendMessage(TextFormat::RED . "The claim you selected must be more than 5x5!");
					$player->setClaiming(false);
					$player->setFirstClaimingPosition();
					$player->setSecondClaimingPosition();
					$player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
					return;
				}
				$amount = $length * $width;
				$price = $amount * 5;
				if($player->isSneaking()) {
					$data = ["name" => $player->getFaction()->getName(), "x1" => $firstPosition->x, "z1" => $firstPosition->z, "x2" => $secondPosition->x, "z2" => $secondPosition->z, "level" => $firstPosition->level->getName()];
					$claim = new Claim(HCF::getInstance(), $data);
					if(ClaimManager::getInstance()->getClaimIntersectsWith($firstPosition, $secondPosition) !== null) {
						$player->setClaiming(false);
						$player->setFirstClaimingPosition();
						$player->setSecondClaimingPosition();
						$player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
						$player->sendMessage(TextFormat::RED . "You can't override a claim!");
						return;
					}
					if($player->getFaction()->getBalance() < $price) {
						$player->sendMessage(TextFormat::RED . "Your faction doesn't have enough money!");
					} else {
						ClaimManager::getInstance()->addClaim($claim);
						$player->getFaction()->claim($claim);
						$claim->viewMap($player);
						$player->getFaction()->removeBalance($price);
						$player->sendMessage(TextFormat::GREEN . "You've successfully claimed this part of land!");
					}
					$player->setClaiming(false);
					$player->setFirstClaimingPosition();
					$player->setSecondClaimingPosition();
					$player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
				} else {
					$player->sendMessage(TextFormat::GRAY . "You've selected " . TextFormat::GREEN . $amount . TextFormat::GRAY . " blocks. Your price is " . TextFormat::YELLOW . $price . TextFormat::GRAY . ". Sneak and tap anywhere to confirm purchase of claim.");
				}
			}
		}
	}
	
	public function onPlayerInteractEventOP(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$item = $event->getItem();
		if($player->hasOpClaim()) {
			if($item->getId() !== ItemIds::DIAMOND_AXE) {
				return;
			}
			if($player->hasOpClaim() === false) {
				$player->setClaiming(false);
				$player->setFirstClaimingPosition();
				$player->setSecondClaimingPosition();
				$player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE));
				return;
			}
			if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
				if(ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
					$player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
					return;
				}
				if($player->isSneaking()) {
					return;
				}
				$player->setFirstClaimingPosition($block->getPosition());
				$player->sendMessage(TextFormat::GRAY . "You've successfully the claim's " . TextFormat::GREEN . "first" . TextFormat::GRAY . " position!");
			} elseif($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
				if(ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
					$player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
					return;
				} 
				if($player->isSneaking()) {
					return;
				}
				$player->setSecondClaimingPosition($block->getPosition());
				$player->sendMessage(TextFormat::GRAY . "You've successfully the claim's " . TextFormat::GREEN . "second" . TextFormat::GRAY . " position!");
			}
			if($player->getFirstClaimPosition() !== null and $player->getSecondClaimPosition() !== null) {
				$firstPosition = $player->getFirstClaimPosition();
				$secondPosition = $player->getSecondClaimPosition();
				if($player->isSneaking()) {
					$data = ["name" => $player->getOpClaimName(), "x1" => $firstPosition->x, "z1" => $firstPosition->z, "x2" => $secondPosition->x, "z2" => $secondPosition->z, "level" => $firstPosition->level->getName()];
					$claim = new Claim(HCF::getInstance(), $data);
					ClaimManager::getInstance()->createClaim($claim);
					$player->sendMessage(TextFormat::GREEN . "You've successfully claimed this part of land, claim name {$player->getOpClaimName()}!");
					$player->setOpClaim(false);
					$player->setFirstClaimingPosition();
					$player->setSecondClaimingPosition();
					$player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE));
					return;
				} else {
					$player->sendMessage(TextFormat::GRAY . "Sneak and tap anywhere to confirm purchase of claim.");
				}
			}
		}
	}
	
	public function onEntityTeleportEvent(EntityTeleportEvent $event) : void {
		$to = $event->getTo();
		$player = $event->getEntity();
		if(!$player instanceof HCFPlayer) {
			return;
		}
		if(!$player->getCooldown()->has('combattag')) {
			return;
		}
		if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($to->asPosition()), "Spawn") !== false) {
			$event->cancel();
			$player->sendMessage(TextFormat::RED . "YOU CAN'T ENTER SAFEZONE WHILE IN COMBAT!");
		}
		/*if($event->isCancelled()) return;
		$dimension = 1;
		if($to->getWorld()->getFolderName() === HCFUtils::NETHER_MAP) {
			$dimension = 0;
		}
		$player->sendDimension($dimension);*/
	}
	
	public function onDeathData(PlayerDeathEvent $event): void{
		$player = $event->getPlayer();
		$player->saveInventory();
		if(stripos(ClaimManager::getInstance()->getClaimNameByPosition($player->getPosition()), "Spawn") !== false && EOTWManager::isEnabled() === false) return;
		$cause = $player->getLastDamageCause();
		$player->setInvincible();
		if(($faction = $player->getFaction()) instanceof Faction) {
			$faction->removeDTR(1);
			$faction->removePoints(1);
		}
		$damager = HCF::getInstance()->getCombatManager()->getTagDamager($player);
		if($damager !== null) {
			$killer = Server::getInstance()->getPlayerExact($damager);
			if($killer instanceof HCFPlayer) {
				$killer->obtainKill($player->getName());
				if(($faction = $killer->getFaction()) instanceof Faction) {
					$faction->addKill(1);
					$faction->addPoints(1);
                    if($player->getFaction() instanceof Faction and $player->getFaction()?->getDtr() === 0) {
                        $faction->addPoints(3);
                    }
				}
				$event->setDeathMessage(TextFormat::colorize("&c" . $player->getName() . "&7[&4" . PlayerData::getKills($player->getName()) . "&7] &ewas slain by &c" . $killer->getName() . "&7[&4" . PlayerData::getKills($killer->getName()) . "&7]&e using &c" . $killer->getHandName()));
			}
		} else {
			if($cause instanceof EntityDamageByEntityEvent) {
				$killer = $cause->getDamager();
				if($killer instanceof HCFPlayer) {
					$killer->obtainKill($player->getName());
					if(($faction = $killer->getFaction()) instanceof Faction) {
						$faction->addKill(1);
						$faction->addPoints(1);
                        if($player->getFaction() instanceof Faction and $player->getFaction()?->getDtr() === 0) {
                            $faction->addPoints(3);
                        }
					}
					$event->setDeathMessage(TextFormat::colorize("&c" . $player->getName() . "&7[&4" . PlayerData::getKills($player->getName()) . "&7] &ewas slain by &c" . $killer->getName() . "&7[&4" . PlayerData::getKills($killer->getName()) . "&7]&e using &c" . $killer->getHandName()));
				}
			} else {
				$event->setDeathMessage(TextFormat::colorize("&c" . $player->getName() . "&7[&4" . PlayerData::getKills($player->getName()) . "&7] &esome how die!"));
			}
		}
		$player->getWorld()->addParticle($player->getPosition(), new HugeExplodeSeedParticle());
		$player->getWorld()->addSound($player->getPosition(), new ExplodeSound());
		if($player->getCooldown()->has('combattag')) {
			$player->getCooldown()->remove('combattag');
		}
	}
	
	public function onRespawn(PlayerRespawnEvent $event): void{
	    $player = $event->getPlayer();
		if(EOTWManager::isEnabled() === false) {
			$event->setRespawnPosition(new Position(0, 100, 0, Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::DEFAULT_MAP)));
			if($player->getCooldown()->has('combattag')) {
				$player->getCooldown()->remove('combattag');
			}
			$player->setArcherMark(false);
			$player->activateEffects(true);
		} else {
			$player->setGamemode(GameMode::SPECTATOR());
		}
	}
	/*
	public function onTransaction(InventoryTransactionEvent $event): void {
		$transaction = $event->getTransaction();
		$actions = array_values($transaction->getActions());
		if (count($actions) === 2) {
			foreach ($actions as $i => $action) {
				if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getId() === ItemIds::ENCHANTED_BOOK  && $action->getTargetItem()->getNamedTag()->getCompoundTag(InventoryUtils::CUSTOM_ENCHANTMENT) instanceof CompoundTag && ($itemClicked = $action->getSourceItem())->getId() !== ItemIds::AIR) {
					if (count($itemClickedWith->getEnchantments()) < 1) return;
					$enchantmentSuccessful = false;
					foreach ($itemClickedWith->getEnchantments() as $enchantment) {
						$newLevel = $enchantment->getLevel();
						if (($existingEnchant = $itemClicked->getEnchantment($enchantment->getType())) !== null) {
							if ($existingEnchant->getLevel() > $newLevel) {
                                if ($action instanceof PlayerInventory){
                                    if($action->getHolder() instanceof HCFPlayer) {
                                        $action->getHolder()->sendMessage(TextFormat::RED . "The enchantment of this book is less than that of the item!");
                                    }
                                }
							    continue;
                            }
							$newLevel = $existingEnchant->getLevel() === $newLevel ? $newLevel + 1 : $newLevel;
							if($newLevel > $existingEnchant->getType()->getMaxLevel()) {
								if ($action instanceof PlayerInventory){
                                    if($action->getHolder() instanceof HCFPlayer) {
                                        $action->getHolder()->sendMessage(TextFormat::RED . "Maximum enchantment level!");
                                    }
                                }
								continue;
							}
						}
						if($enchantment->getType() instanceof CustomEnchantment){
							if($enchantment->getType()->canEnchant($itemClicked) === false) {
                                $p = $otherAction->getInventory();
                                if($p instanceof PlayerInventory) {
                                    if($p->getHolder() instanceof HCFPlayer) {
                                        $p->getHolder()->sendMessage(TextFormat::RED . "This item cannot be enchanted with this book.!");
                                    }
                                }
								continue;
							}
						}
						$rand1 = rand(0, 100);
						if($rand1 <= 60) {
							$event->cancel();
							$otherAction->getInventory()->setItem($otherAction->getSlot(), ItemFactory::air());
                            $p = $otherAction->getInventory();
							if($p instanceof PlayerInventory) {
								if($p->getHolder() instanceof HCFPlayer) {
									$p->getHolder()->sendMessage(TextFormat::RED . "Oh :( this book don't like that item!");
								}
							}
							continue;
						}
						$itemClicked->addEnchantment($enchantment->getType()->setLevel($newLevel));
                        $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                        $enchantmentSuccessful = true;
                    }
                    if ($enchantmentSuccessful) {
                        $event->cancel();
                        $otherAction->getInventory()->setItem($otherAction->getSlot(), ItemFactory::air());
                        $p = $otherAction->getInventory();
                        if($p instanceof PlayerInventory) {
                        	if($p->getHolder() instanceof HCFPlayer) {
                        		$p->getHolder()->playXpLevelUpSound();
                     		   $p->getHolder()->sendMessage(TextFormat::GREEN . "Your item loved this book and accepted it!");
                        	}
                        }
                    }
                }
            }
        }
    }*/

    public function onTransaction(InventoryTransactionEvent $event): void
    {
        $transaction = $event->getTransaction();
        $actions = array_values($transaction->getActions());
        if (count($actions) === 2) {
            foreach ($actions as $i => $action) {
                if ($action instanceof SlotChangeAction && ($otherAction = $actions[($i + 1) % 2]) instanceof SlotChangeAction && ($itemClickedWith = $action->getTargetItem())->getId() === ItemIds::ENCHANTED_BOOK && ($itemClicked = $action->getSourceItem())->getId() !== ItemIds::AIR) {
                    if (count($itemClickedWith->getEnchantments()) < 1) return;
                    $enchantmentSuccessful = false;
                    foreach ($itemClickedWith->getEnchantments() as $enchantment) {
                        $currentLevel = $enchantment->getLevel();
                        $enchantmentType = $enchantment->getType();
                        if (($existingEnchant = $itemClicked->getEnchantment($enchantment->getType())) !== null) {
                            if ($existingEnchant->getLevel() > $currentLevel) continue;
                            $currentLevel = $existingEnchant->getLevel() === $currentLevel ? $currentLevel + 1 : $currentLevel;
                        }
                        if($enchantmentType instanceof CustomEnchantment) {
                            if (!$enchantmentType->canEnchant($itemClicked) and ($itemClickedWith->getId() !== ItemIds::ENCHANTED_BOOK)) continue;
                        }
                        $itemClicked->addEnchantment(new EnchantmentInstance($enchantment->getType(), $currentLevel));
                        CustomEnchantments::displayEnchantsOld($itemClicked);
                        $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                        $enchantmentSuccessful = true;
                    }
                    if ($enchantmentSuccessful) {
                        $event->cancel();
                        $otherAction->getInventory()->setItem($otherAction->getSlot(), ItemFactory::air());
                        $p = $action->getInventory();
                        if($p instanceof PlayerInventory) {
                            if($p->getHolder() instanceof Player) {
                                $p->getHolder()->playXpLevelUpSound();
                                $p->getHolder()->sendMessage(TextFormat::GREEN . "Your item loved this book and accepted it!");
                            }
                        }
                    }
                }
            }
        }
    }

    public function onKick(PlayerKickEvent $event) : void
    {
        $player = $event->getPlayer();
        $reason  = $event->getReason();
        if ($reason == PlayerPreLoginEvent::KICK_REASON_SERVER_WHITELISTED){
            $event->setQuitMessage(TextFormat::DARK_PURPLE . "Now we are closed, try to join again later!");
        }
    }
    
    public function onPlayerItemConsumeEvent(PlayerItemConsumeEvent $event) : void {
		$player = $event->getPlayer();
		$item = $event->getItem();
		if($item->getId() === ItemIds::APPLEENCHANTED){
			$time = (900 - (time() - PlayerData::getCountdown($player->getName(), "appleenchanted")));
			if($time > 0){
				$player->sendTip(TextFormat::RED . "You may not eat a " . TextFormat::GOLD . "NOTCH " . TextFormat::RED . "for " . TextFormat::GOLD . HCFUtils::getTimeString(PlayerData::getCountdown($player->getName(), "appleenchanted")));
				$event->cancel();
			}else{
				PlayerData::setCountdown($player->getName(), 'appleenchanted', (time() + 900));
			}
		}
		if($item->getId() === ItemIds::GOLDEN_APPLE){
			if($player->getCooldown()->has('golden_apple')) {
				$player->sendTip(TextFormat::RED . "You may not eat a " . TextFormat::GOLD . "GOLDEN APPLE " . TextFormat::RED . "for " . TextFormat::GOLD . $player->getCooldown()->get('golden_apple'));
				$event->cancel();
				return;
			}
			$countdown = 30;
            $enchant = $player->getArmorInventory()->getChestplate()->getEnchantment(new Gappler());
			if($enchant !== null){
			    $countdown = $enchant->getType()->calculateTime($enchant->getLevel());
            }
			$player->getCooldown()->add('golden_apple', $countdown);
		}
	}
	
	public function onSignChange(SignChangeEvent $event) : void {
        if(strtolower($event->getNewText()->getLine(0)) != "[elevator]"){
            return;
        }
        if(strtolower($event->getNewText()->getLine(1)) == "up" || strtolower($event->getNewText()->getLine(1)) == "down"){
            $event->getSign()->setText(new SignText([
                0 => TextFormat::RED . "[Elevator]" . TextFormat::RESET,
                1 => strtolower($event->getNewText()->getLine(1)),
                2 => "",
                3 => ""
            ]));
            $event->getPlayer()->sendMessage(TextFormat::GRAY . "You have created an elevator and remember that there must be 2 non-solid blocks for the elevator to work.!");
 	   }
	}
	
	public function onSignElevator(PlayerInteractEvent $event) : void {
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$vec = new Vector3($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
		$tile = $player->getWorld()->getTile($vec);
		if($tile instanceof Sign){
			$line = $tile->getText();
			if($line[0] === TextFormat::RED . "[Elevator]" . TextFormat::RESET){
				if(!$player->isSneaking()){
					$event->cancel();
					if(strtolower($line[1]) == "up"){
						if(($vector = $this->getSafestUpSignBlock($block)) instanceof Vector3) {
							$player->teleport($vector);
						} else {
							$player->sendMessage(TextFormat::RED . "Remember that there must be 2 non-solid blocks for the elevator to work.!"); 
						}
					} elseif(strtolower($line[1]) == "down"){
						if(($vector = $this->getSafestDownSignBlock($block)) instanceof Vector3) {
							$player->teleport($vector);
						} else {
							$player->sendMessage(TextFormat::RED . "Remember that there must be 2 non-solid blocks for the elevator to work.!"); 
						}
					}
				}
			}
		}
	}
	
	public function onSignShopChange(SignChangeEvent $event) : void {
		$player = $event->getPlayer();
		if(!$player->hasPermission("configurate.shop") && $player->getGamemode() !== 1){
			return;
		}
		if(strtolower($event->getNewText()->getLine(0)) == "[shop]" && $player->hasPermission("configurate.shop")){
			if(strtolower($event->getNewText()->getLine(1)) == "buy"){
				$items = explode(":", $event->getNewText()->getLine(2));
				if(count($items) === 3){
					if($items[2] >= 1){ 
						$item = ItemFactory::getInstance()->get((int)$items[0], (int)$items[1], (int)$items[2]);
						$name = $item->getName() == "Enchanted Golden Apple" ? "Gapple" : $item->getName();
                        $event->getSign()->setText(new SignText([
                            0 => TextFormat::GREEN . "~" . strtoupper($event->getNewText()->getLine(1)) . "~" . TextFormat::RESET,
                            1 => str_replace(["Lazuli ", "Stained "], "", $name),
                            2 => $item->getId() . ":" .$item->getMeta().":".$item->getCount(),
                            3 => "$" . $event->getNewText()->getLine(3)
                        ]));
					}
				}
			} elseif(strtolower($event->getNewText()->getLine(1)) == "sell" && $player->hasPermission("configurate.shop")){
				$items = explode(":", $event->getNewText()->getLine(2));
				if(count($items) === 3){
					if($items[2] >= 1){
						$item = ItemFactory::getInstance()->get((int)$items[0], (int)$items[1], (int)$items[2]);
						$name = $item->getName() == "Enchanted Golden Apple" ? "Gapple" : $item->getName();
                        $event->getSign()->setText(new SignText([
                            0 => TextFormat::RED . "~" . strtoupper($event->getNewText()->getLine(1)) . "~" . TextFormat::RESET,
                            1 => str_replace(["Lazuli ", "Stained "], "", $name),
                            2 => $item->getId() . ":" .$item->getMeta().":".$item->getCount(),
                            3 => "$" . $event->getNewText()->getLine(3)
                        ]));
					}
				}
			}
 	   }
	}
	
	public function onSign(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		$vec = new Vector3($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
		$tile = $player->getWorld()->getTile($vec);
		if($tile instanceof Sign){
			$line = $tile->getText();
			if($line->getLine(0) == TextFormat::GREEN . "~BUY~" . TextFormat::RESET){
				if(!$player->isSneaking()){
					if($player->getBalance() >= $this->getPrice($line->getLine(3))){
						if($player->getInventory()->canAddItem($this->getItem($line->getLine(2)))){
							$player->reduceBalance($this->getPrice($line->getLine(3)));
							$item = $this->getItem($line->getLine(2));
							$player->getInventory()->addItem($item);
							$player->sendMessage(TextFormat::GREEN . "Successfully purchased x". $item->getCount() ." ". $item->getName() . "!");
						} else {
							$player->sendMessage(TextFormat::RED . "Your inventory is full!");
						}
					} else {
						$player->sendMessage(TextFormat::RED . "You do not have enough money to make this purchase.");
					}
				}
			} elseif($line->getLine(0) == TextFormat::RED . "~SELL~" . TextFormat::RESET){
				if(!$player->isSneaking()){
					$item = $this->getItem($line->getLine(2));
					if($player->getItemCount($item) >= $item->getCount()){
						$player->getInventory()->removeItem($item);
						$player->addBalance($this->getPrice($line->getLine(3)));
						$player->sendMessage(TextFormat::GREEN . "Sold {$item->getCount()}x " . $item->getName() . " for " . $this->getPrice($line->getLine(3)));
					} else {
						$player->sendMessage(TextFormat::RED . "You do not have the required items in your inventory.");
					}
				}
			}
		}
	}
	
	public function onCombatLoggerDamage(EntityDamageEvent $event) : void {
		$logger = $event->getEntity();
		if($logger instanceof CombatLogger && $event instanceof EntityDamageByEntityEvent){
			$damager = $event->getDamager();
			if($damager instanceof HCFPlayer) {
				if($logger->getFaction() !== null && HCF::getInstance()->getFactionManager()->equalFaction($damager->getFaction(), $logger->getFaction())) {
					$event->cancel();
				} else {
					$logger->lastDamager = $damager;
				}
			}
		}
	}
	
	public function onInventoryCloseEvent(InventoryCloseEvent $event) : void {
		$player = $event->getPlayer();
		$inventory = $event->getInventory();
		if($inventory instanceof EnderChestInventory){
		    # This is to remove the chest when the player closes the inventory
            $position = $inventory->getHolder();

            if($player->getReplaceableBlock() !== 0) {
                $blockRuntimeId = $player->getReplaceableBlock();
            } else {
                $blockRuntimeId = RuntimeBlockMapping::getInstance()->fromRuntimeId(BlockFactory::getInstance()->get(BlockLegacyIds::BEDROCK, 0)->getFullId());
            }
            $pk = UpdateBlockPacket::create(new BlockPosition($position->x,$position->y, $position->z), $blockRuntimeId, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
            $player->getNetworkSession()->sendDataPacket($pk);
        }
	}

    public function onCraft(CraftItemEvent $event) : void
    {
        $outputs = $event->getOutputs();
        foreach ($outputs as $output) {
            if($output->getId() == BlockLegacyIds::BREWING_STAND_BLOCK or $output->getId() == BlockLegacyIds::TNT){
                $event->cancel();
                $event->getPlayer()->sendMessage(TextFormat::RED . "You cant craft this item");
            }
        }
	}
	
	
	public function getItem(string $text) : Item{
		$values = explode(":", $text);
		return ItemFactory::getInstance()->get($values[0], $values[1], $values[2]);
	}
	
	public function getPrice(string $text) : int{
		return str_replace("$", "", $text);
	}
    
    public function getSafestUpSignBlock(Block $block) : ? Vector3 {
        $vector = null;
        for($i = $block->getPosition()->getFloorY()+1; $i < 256; $i++){
            if ($block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ()))->getId() == 0 and $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i+1, $block->getPosition()->getZ()))->getId() == 0) {
                $vector = new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ());
                break;
            }
        }
        return $vector;
    }
    
    public function getSafestDownSignBlock(Block $block) : ? Vector3 {
        $vector = null;
        for($i = $block->getPosition()->getFloorY()-1; $i > 0; $i--){
            if ($block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ()))->getId() == 0 and $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i+1, $block->getPosition()->getZ()))->getId() == 0) {
                $vector = new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ());
                break;
            }
        }
        return $vector;
    }
	
}