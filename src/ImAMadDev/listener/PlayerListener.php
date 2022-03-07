<?php

namespace ImAMadDev\listener;

use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\customenchants\CustomEnchantments;
use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;
use ImAMadDev\player\{HCFPlayer, PlayerCache, PlayerUtils, PlayerData};
use ImAMadDev\ticks\asynctask\SaveSkinAsyncTask;
use ImAMadDev\manager\{EOTWManager, ClaimManager, SOTWManager};
use ImAMadDev\entity\CombatLogger;
use ImAMadDev\faction\Faction;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\customenchants\CustomEnchantment;

use pocketmine\block\BaseSign;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityEffectRemoveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\{PlayerItemUseEvent,
    PlayerJoinEvent,
    PlayerMoveEvent,
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
    InventoryTransactionEvent};
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\{BlockActorDataPacket,
    ContainerClosePacket,
    InventoryContentPacket,
    InventorySlotPacket,
    InventoryTransactionPacket,
    MobEquipmentPacket,
    MobEffectPacket,
    NetworkChunkPublisherUpdatePacket,
    types\BlockPosition,
    types\CacheableNbt,
    types\inventory\ItemStackWrapper};
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\World;
use pocketmine\scheduler\ClosureTask;

class PlayerListener implements Listener
{

    private bool $cancel_send = true;


    /**
     * @param DataPacketSendEvent $event
     * @priority NORMAL
     * @ignoreCancelled true
     */
    public function onDataPacketSend(DataPacketSendEvent $event): void
    {
        foreach ($event->getPackets() as $packet) {
            if ($this->cancel_send && $packet instanceof ContainerClosePacket) {
                $event->cancel();
            }
        }
    }
    
    public function onEntityEffectRemoveEvent(EntityEffectRemoveEvent $event): void
    {
    	$player = $event->getEntity();
    	$effect = $event->getEffect();
    	if ($player instanceof HCFPlayer) {
    		HCF::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function ()use($player, $effect){
         	   $player->checkClassEffects($effect);
    	    }), (2*20));
        }
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @priority NORMAL
     * @ignoreCancelled true
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        if ($packet instanceof ContainerClosePacket) {
            $this->cancel_send = false;
            $event->getOrigin()->sendDataPacket($packet, false);
            $this->cancel_send = true;
        }
    }

    public function handleItemPickup(EntityItemPickupEvent $event): void
    {
        $player = $event->getOrigin();
        if ($player instanceof HCFPlayer) {
            if ($player->isInvincible() or !$player->isAlive()) {
                $event->cancel();
            }
        }
    }
    
    public function handleSend(DataPacketSendEvent $event): void
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
    }

    public function handleReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        if ($packet instanceof InventoryTransactionPacket) {
            $transaction = $packet->trData;
            foreach ($transaction->getActions() as $action) {
                $action->oldItem = new ItemStackWrapper($action->oldItem->getStackId(), CustomEnchantments::filterDisplayedEnchants($action->oldItem->getItemStack()));
                $action->newItem = new ItemStackWrapper($action->newItem->getStackId(), CustomEnchantments::filterDisplayedEnchants($action->newItem->getItemStack()));
            }
        }
        if ($packet instanceof MobEquipmentPacket) CustomEnchantments::filterDisplayedEnchants($packet->item->getItemStack());
    }

    public function onCreationEvent(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(HCFPlayer::class);
    }

    public function onEntityDamageEvent(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        if ($player instanceof HCFPlayer) {
            if (ClaimManager::getInstance()->getClaimByPosition($player->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN or $player->isInvincible()) {
                if (EOTWManager::isEnabled() === false) {
                    $event->cancel();
                    return;
                }
            }
            if ($event instanceof EntityDamageByEntityEvent) {
                $attacker = $event->getDamager();
                if ($attacker instanceof HCFPlayer) {
                    if (ClaimManager::getInstance()->getClaimByPosition($attacker->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN or $player->isInvincible() or $attacker->isInvincible()) {
                        if (EOTWManager::isEnabled() === false) {
                            $event->cancel();
                        }
                    }
                }
            }
        }
    }

    public function onJoinEvent(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player instanceof HCFPlayer) return;
        PlayerData::register($player->getName());
        $event->setJoinMessage(TextFormat::GRAY . "[" . TextFormat::GREEN . "+" . TextFormat::GRAY . "] " . TextFormat::GREEN . $player->getName());
        $player->load();
        if (!$player->hasPlayedBefore()) {
            $player->setInvincible();
            HCF::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'rank give "' . $player->getName() . '" Waffle 3h');
            $player->sendMessage(TextFormat::colorize("&7Welcome to &gWaffleHCF &c&lBETA 2.0.\n&eYou have received the &g[Waffle] &r&erank for &c3 hours&e."));
            HCFUtils::firstJoin($player);
        }
        $player->setInvincible($player->getCache()->getInData('invincibility_time', true));
        $player->getFaction()?->message(TextFormat::GREEN . "+ Member online: " . TextFormat::RED . $player->getName());
        $player->getServer()->getAsyncPool()->submitTask(new SaveSkinAsyncTask($player->getName(), $player->getSkin()));
        $player->setJoined(true);
    }

    public function onQuitEvent(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $event->setQuitMessage(TextFormat::GRAY . "[" . TextFormat::RED . "-" . TextFormat::GRAY . "] " . TextFormat::RED . $player->getName());
        if ($player->getCache() instanceof PlayerCache) {
            $player->getCache()->saveData();
            if ($player->canLogout() == true) {
                return;
            }
            if (SOTWManager::isEnabled()) {
                return;
            }
            if (!$player->isAlive()) {
                return;
            }
            if (ClaimManager::getInstance()->getClaimByPosition($player->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN && !EOTWManager::isEnabled()) {
                return;
            }
            if ($event->getQuitReason() === "Server Closed" || $event->getQuitReason() === "Internal server error") {
                return;
            }
            if ($player->getCooldown()->has('combattag')) {
                $player->kill();
                return;
            }
            $time = $player->hasPermission("vip.combatlogger") === true ? 400 : 500;
            $faction = $player->getFaction() === null ? "" : $player->getFaction()->getName();
            $entity = new CombatLogger($player->getLocation());
            $entity->setFaction($faction);
            $entity->setPlayer($player->getName());
            $entity->setNameTagAlwaysVisible(true);
            $entity->setNameTagVisible(true);
            $entity->load($time);
            $entity->setCanSaveWithChunk(true);
            $entity->setHealth(100.0);
            $entity->setMaxHealth(100);
            $entity->spawnToAll();
        }
    }

    public function onLevelChangeEvent(EntityTeleportEvent $event): void
    {
        $player = $event->getEntity();
        if ($player instanceof HCFPlayer and $event->getFrom()->getWorld()->getFolderName() !== $event->getTo()->getWorld()->getFolderName()) {
            $player->showCoordinates();
        }
    }

    public function onChatEvent(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof HCFPlayer) {
            switch ($player->getChatMode()->get()) {
                case PlayerUtils::PUBLIC:
                    $event->setFormat($player->getChatFormat() . $player->getName() . $player->getCurrentTagFormat() . ': ' . $event->getMessage());
                    break;
                case PlayerUtils::FACTION:
                    if ($player->getFaction() !== null) {
                        $event->cancel();
                        $player->getFaction()->message(TextFormat::DARK_GREEN . "[Faction] " . $player->getName() . ": " . TextFormat::GOLD . $event->getMessage());
                    } else {
                        $event->setFormat($player->getChatFormat() . $player->getName() . $player->getCurrentTagFormat() . ': ' . $event->getMessage());
                    }
                    break;
                case PlayerUtils::ALLY:
                    if ($player->getFaction() !== null) {
                        $event->cancel();
                        foreach ($player->getFaction()->getAllies() as $ally) {
                            if (($allyClass = HCF::getInstance()->getFactionManager()->getFaction($ally)) instanceof Faction) {
                                $allyClass->message(TextFormat::DARK_GREEN . "[" . $player->getFaction()->getName() . "] [ALLY] " . $player->getName() . ": " . TextFormat::GOLD . $event->getMessage());
                            }
                        }
                        $player->getFaction()->message(TextFormat::DARK_GREEN . "[ALLY] " . $player->getName() . ": " . TextFormat::GOLD . $event->getMessage());
                    } else {
                        $event->setFormat($player->getChatFormat() . $player->getName() . $player->getCurrentTagFormat() . ': ' . $event->getMessage());
                    }
                    break;
                case PlayerUtils::STAFF:
                    $event->cancel();
                    foreach (Server::getInstance()->getOnlinePlayers() as $staff) {
                        if ($staff->getChatMode()->get() == PlayerUtils::STAFF) {
                            $staff->sendMessage(TextFormat::DARK_AQUA . '[MOD-CHAT] ' . $player->getName() . ': ' . $event->getMessage());
                        }
                    }
                    break;
            }
        }
    }
    
    public function handleBorder(PlayerMoveEvent $event): void 
    {
    	$player = $event->getPlayer();
	    $to = $event->getTo();
    	if (abs($to->getX()) >= 1500 and abs($to->getZ()) >= 1500) {
    		$event->cancel();
    		$player->sendMessage(TextFormat::RED . "Border reached!");
        }
    }
    
    public function handleBorderTeleport(EntityTeleportEvent $event): void 
    {
	    $to = $event->getTo();
		$player = $event->getEntity();
        if (!$player instanceof HCFPlayer) return;
    	if (abs($to->getX()) >= 1500 and abs($to->getZ()) >= 1500) {
    		$event->cancel();
    		$player->sendMessage(TextFormat::RED . "Border reached!");
        }
    }

    public function onPlayerInteractEvent(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();
        if ($player instanceof HCFPlayer) {
            if ($player->getClaimSession() === null) return;
            if ($player->getClaimSession()->isOpClaim() == false) {
                if ($item->getId() !== ItemIds::WOODEN_AXE) {
                    return;
                }
                if ($player->getFaction() === null or !$player->getFaction()->isLeader($player->getName())) {
                    $player->setClaimSession();
                    $player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
                    return;
                }
                if (abs($player->getPosition()->getX()) <= 500 and abs($player->getPosition()->getZ()) <= 500) {
                    $player->sendMessage(TextFormat::RED . "You can only claim when you are 500 blocks from spawn!");
                    return;
                }
                if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                    if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
                        $player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
                        return;
                    }
                    if ($player->isSneaking()) {
                        return;
                    }
                    $player->getClaimSession()->setPosition1($block->getPosition());
                    $player->sendMessage(TextFormat::GRAY . "You've successfully the claim's " . TextFormat::GREEN . "first" . TextFormat::GRAY . " position!");
                } elseif ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
                        $player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
                        return;
                    }
                    if ($player->isSneaking()) {
                        return;
                    }
                    $player->getClaimSession()->setPosition2($block->getPosition());
                    $player->sendMessage(TextFormat::GRAY . "You've successfully the claim's " . TextFormat::GREEN . "second" . TextFormat::GRAY . " position!");
                }
            }
        }
    }

    public function handleRegisterClaim(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($player instanceof HCFPlayer) {
            if ($player->getClaimSession() === null) return;
            if ($player->getClaimSession()->isOpClaim() == false) {
                if ($item->getId() !== ItemIds::WOODEN_AXE) {
                    return;
                }
                if ($player->getClaimSession()->getPosition1() !== null and $player->getClaimSession()->getPosition2() !== null) {
                    $firstPosition = $player->getClaimSession()->getPosition1();
                    $firstX = $firstPosition->getX();
                    $firstZ = $firstPosition->getZ();
                    $secondPosition = $player->getClaimSession()->getPosition2();
                    $secondX = $secondPosition->getX();
                    $secondZ = $secondPosition->getZ();
                    $length = max($firstX, $secondX) - min($firstX, $secondX);
                    $width = max($firstZ, $secondZ) - min($firstZ, $secondZ);
                    if ($length <= 5 or $width <= 5) {
                        $player->sendMessage(TextFormat::RED . "The claim you selected must be more than 5x5!");
                        $player->setClaimSession();
                        $player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
                        return;
                    }
                    $amount = $length * $width;
                    $price = $amount * 5;
                    if ($player->isSneaking()) {
                        $data = ["name" => $player->getFaction()->getName(), "x1" => $firstPosition->x, "z1" => $firstPosition->z, "x2" => $secondPosition->x, "z2" => $secondPosition->z, "level" => $firstPosition->getWorld()->getFolderName()];
                        $claim = new Claim(HCF::getInstance(), $data);
                        if (ClaimManager::getInstance()->getClaimIntersectsWith($firstPosition, $secondPosition) !== null) {
                            $player->setClaimSession();
                            $player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
                            $player->sendMessage(TextFormat::RED . "You can't override a claim!");
                            return;
                        }
                        if ($player->getFaction()->getBalance() < $price) {
                            $player->sendMessage(TextFormat::RED . "Your faction doesn't have enough money!");
                        } else {
                            ClaimManager::getInstance()->addClaim($claim);
                            $player->getFaction()->claim($claim);
                            $claim->viewMap($player);
                            $player->getFaction()->removeBalance($price);
                            $player->sendMessage(TextFormat::GREEN . "You've successfully claimed this part of land!");
                        }
                        $player->setClaimSession();
                        $player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::WOODEN_AXE));
                    } else {
                        $player->sendMessage(TextFormat::GRAY . "You've selected " . TextFormat::GREEN . $amount . TextFormat::GRAY . " blocks. Your price is " . TextFormat::YELLOW . $price . TextFormat::GRAY . ". Sneak and tap anywhere to confirm purchase of claim.");
                    }
                }
            }
            if ($player->getClaimSession() === null) return;
            if ($player->getClaimSession()->isOpClaim()) {
                if ($item->getId() !== ItemIds::DIAMOND_AXE) {
                    return;
                }
                if ($player->getClaimSession()->getPosition1() !== null and $player->getClaimSession()->getPosition2() !== null) {
                    $firstPosition = $player->getClaimSession()->getPosition1();
                    $secondPosition = $player->getClaimSession()->getPosition2();
                    if ($player->isSneaking()) {
                        $data = ["name" => $player->getClaimSession()->getName(), "x1" => $firstPosition->x, "z1" => $firstPosition->z, "x2" => $secondPosition->x, "z2" => $secondPosition->z, "level" => $firstPosition->getWorld()->getFolderName(), 'claim_type' => $player->getClaimSession()->getType()];
                        $claim = new Claim(HCF::getInstance(), $data);
                        ClaimManager::getInstance()->createClaim($claim);
                        $player->sendMessage(TextFormat::GREEN . "You've successfully claimed this part of land, claim name {$player->getClaimSession()->getName()}!");
                        $player->setClaimSession();
                        $player->getInventory()->remove(ItemFactory::getInstance()->get(ItemIds::DIAMOND_AXE));
                    } else {
                        $player->sendMessage(TextFormat::GRAY . "Sneak and tap anywhere to confirm purchase of claim.");
                    }
                }
            }
        }
    }

    public function onPlayerInteractEventOP(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $event->getItem();
        if ($player instanceof HCFPlayer) {
            if ($player->getClaimSession() === null) return;
            if ($player->getClaimSession()->isOpClaim()) {
                if ($item->getId() !== ItemIds::DIAMOND_AXE) {
                    return;
                }
                if ($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK) {
                    if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
                        $player->sendMessage(TextFormat::RED . "You cant claim here because is a protected zone!");
                        return;
                    }
                    if ($player->isSneaking()) {
                        return;
                    }
                    $player->getClaimSession()->setPosition1($block->getPosition());
                    $player->sendMessage(TextFormat::GRAY . "You've successfully put the claim's " . TextFormat::GREEN . "first" . TextFormat::GRAY . " position!");
                } elseif ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition()) !== null) {
                        $player->sendMessage(TextFormat::RED . "You can only claim here because is a protected zone!");
                        return;
                    }
                    if ($player->isSneaking()) {
                        return;
                    }
                    $player->getClaimSession()->setPosition2($block->getPosition());
                    $player->sendMessage(TextFormat::GRAY . "You've successfully put the claim's " . TextFormat::GREEN . "second" . TextFormat::GRAY . " position!");
                }
            }
        }
    }

    public function onEntityTeleportEvent(EntityTeleportEvent $event): void
    {
        $to = $event->getTo();
        $player = $event->getEntity();
        if (!$player instanceof HCFPlayer) {
            return;
        }
        if (!$player->getCooldown()->has('combattag')) {
            return;
        }
        if (ClaimManager::getInstance()->getClaimByPosition($to->asPosition())?->getClaimType()->getType() == ClaimType::SPAWN) {
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

    public function onDeathData(PlayerDeathEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof HCFPlayer) {
            $player->saveInventory();
            if (ClaimManager::getInstance()->getClaimByPosition($player->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN && EOTWManager::isEnabled() === false) return;
            $cause = $player->getLastDamageCause();
            $player->setInvincible();
            if (($faction = $player->getFaction()) instanceof Faction) {
                $faction->removeDTR(1);
                $faction->removePoints(1);
            }
            $attacker = HCF::$combatManager::getTagAttacker($player);
            if ($attacker !== null) {
                $killer = Server::getInstance()->getPlayerExact($attacker);
                if ($killer instanceof HCFPlayer) {
                    $killer->obtainKill($player->getName());
                    $item = HCFUtils::createDeathSign($player->getName(), HCF::getInstance()->getCombatManager()::getTagAttacker($player));
                    $player->getWorld()->dropItem($player->getPosition(), $item);
                    if (($faction = $killer->getFaction()) instanceof Faction) {
                        $faction->addKill(1);
                        $faction->addPoints(1);
                        if ($player->getFaction() instanceof Faction and $player->getFaction()?->getDtr() === 0) {
                            $faction->addPoints(3);
                        }
                    }
                    $event->setDeathMessage(TextFormat::colorize("&c" . $player->getName() . "&7[&4" . $player->getCache()->getInData('kills', true, 0) . "&7] &ewas slain by &c" . $killer->getName() . "&7[&4" . $killer->getCache()->getInData('kills', true, 0) . "&7]&e using &c" . $killer->getHandName()));
                }
            } else {
                if ($cause instanceof EntityDamageByEntityEvent) {
                    $killer = $cause->getDamager();
                    if ($killer instanceof HCFPlayer) {
                        $killer->obtainKill($player->getName());
                        $item = HCFUtils::createDeathSign($player->getName(), HCF::getInstance()->getCombatManager()->getTagAttacker($player));
                        $player->getWorld()->dropItem($player->getPosition(), $item);
                        if (($faction = $killer->getFaction()) instanceof Faction) {
                            $faction->addKill(1);
                            $faction->addPoints(1);
                            if ($player->getFaction() instanceof Faction and $player->getFaction()?->getDtr() === 0) {
                                $faction->addPoints(3);
                            }
                        }
                        $event->setDeathMessage(TextFormat::colorize("&c" . $player->getName() . "&7[&4" . $player->getCache()->getInData('kills', true, 0) . "&7] &ewas slain by &c" . $killer->getName() . "&7[&4" . $killer->getCache()->getInData('kills', true, 0) . "&7]&e using &c" . $killer->getHandName()));
                    }
                } else {
                    $event->setDeathMessage(TextFormat::colorize("&c" . $player->getName() . "&7[&4" . $player->getCache()->getInData('kills', true, 0) . "&7] &esome how die!"));
                }
            }
            $player->getWorld()->addParticle($player->getPosition(), new HugeExplodeSeedParticle());
            $player->getWorld()->addSound($player->getPosition(), new ExplodeSound());
            if ($player->getCooldown()->has('combattag')) {
                $player->getCooldown()->remove('combattag');
            }
        }
    }

    public function onRespawn(PlayerRespawnEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof HCFPlayer) {
            if (EOTWManager::isEnabled() === false) {
                if (Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::DEFAULT_MAP) instanceof World) {
                    $event->setRespawnPosition(Server::getInstance()->getWorldManager()->getWorldByName(HCFUtils::DEFAULT_MAP)->getSpawnLocation());
                } else {
                    $event->setRespawnPosition(new Position(0, 100, 0, Server::getInstance()->getWorldManager()->getDefaultWorld()));
                }
                if ($player->getCooldown()->has('combattag')) {
                    $player->getCooldown()->remove('combattag');
                }
                $player->getArcherMark()->setDistance(0);
                $player->activateEffects(true);
            } else {
                $player->setGamemode(GameMode::SPECTATOR());
            }
        }
    }

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
                            if ($enchantmentType->getMaxLevel() <= $currentLevel) continue;
                            $currentLevel = $existingEnchant->getLevel() === $currentLevel ? $currentLevel + 1 : $currentLevel;
                        }
                        if ($enchantmentType instanceof CustomEnchantment) {
                            if (!$enchantmentType->canEnchant($itemClicked) or ($itemClickedWith->getId() !== ItemIds::ENCHANTED_BOOK)) continue;
                        }
                        $itemClicked->addEnchantment(new EnchantmentInstance($enchantment->getType(), $currentLevel));
                        $action->getInventory()->setItem($action->getSlot(), $itemClicked);
                        $enchantmentSuccessful = true;
                    }
                    if ($enchantmentSuccessful) {
                        $event->cancel();
                        $otherAction->getInventory()->setItem($otherAction->getSlot(), VanillaItems::AIR());
                        $p = $action->getInventory();
                        $p2 = $action->getInventory();
                        if ($p instanceof PlayerInventory) {
                            if ($p->getHolder() instanceof Player) {
                                $p->getHolder()?->playXpLevelUpSound();
                                $p->getHolder()->sendMessage(TextFormat::GREEN . "Your item loved this book and accepted it!");
                            }
                        } elseif ($p2 instanceof PlayerInventory) {
                            if ($p2->getHolder() instanceof Player) {
                                $p2->getHolder()?->playXpLevelUpSound();
                                $p2->getHolder()->sendMessage(TextFormat::GREEN . "Your item loved this book and accepted it!");
                            }
                        }
                    }
                }
            }
        }
    }

    public function onPlayerItemConsumeEvent(PlayerItemConsumeEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($player instanceof HCFPlayer) {
            if ($item->getId() === ItemIds::APPLEENCHANTED) {
                $time = (900 - (time() - $player->getCache()->getCountdown('appleenchanted')));
                if ($time > 0) {
                    $player->sendMessage(TextFormat::RED . "You may not eat a " . TextFormat::GOLD . "NOTCH " . TextFormat::RED . "for " . TextFormat::GOLD . HCFUtils::getTimeString($player->getCache()->getCountdown('appleenchanted')));
                    $event->cancel();
                } else {
                    $player->getCache()->setCountdown('appleenchanted', 900);
                }
            }
            if ($item->getId() === ItemIds::GOLDEN_APPLE) {
                if ($player->getCooldown()->has('golden_apple')) {
                    $player->sendMessage(TextFormat::RED . "You may not eat a " . TextFormat::GOLD . "GOLDEN APPLE " . TextFormat::RED . "for " . TextFormat::GOLD . $player->getCooldown()->get('golden_apple'));
                    $event->cancel();
                    return;
                }
                $countdown = 30;
                $enchant = $player->getArmorInventory()->getChestplate()->getEnchantment(CustomEnchantments::getEnchantmentByName(CustomEnchantment::GAPPLER));
                if ($enchant !== null) {
                    $countdown = $enchant->getType()->calculateTime($enchant->getLevel());
                }
                $player->getCooldown()->add('golden_apple', $countdown);
            }
        }
    }

    public function onSignElevator(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $vec = new Vector3($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
        $tile = $player->getWorld()->getTile($vec);
        if ($tile instanceof Sign) {
            $line = $tile->getText()->getLines();
            if ($line[0] === TextFormat::RED . "[Elevator]" . TextFormat::RESET) {
                if (!$player->isSneaking()) {
                    $event->cancel();
                    if (strtolower($line[1]) == "up") {
                        if (($vector = $this->getSafestUpSignBlock($block)) instanceof Vector3) {
                            $player->teleport($vector);
                        } else {
                            $player->sendMessage(TextFormat::RED . "Remember that there must be 2 non-solid blocks for the elevator to work.!");
                        }
                    } elseif (strtolower($line[1]) == "down") {
                        if (($vector = $this->getSafestDownSignBlock($block)) instanceof Vector3) {
                            $player->teleport($vector);
                        } else {
                            $player->sendMessage(TextFormat::RED . "Remember that there must be 2 non-solid blocks for the elevator to work.!");
                        }
                    }
                }
            }
        }
    }

    public function onSign(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if ($player instanceof HCFPlayer) {
            $vec = new Vector3($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());
            $tile = $player->getWorld()->getTile($vec);
            if ($tile instanceof Sign) {
                $line = $tile->getText();
                if ($line->getLine(0) == TextFormat::GREEN . "~BUY~" . TextFormat::RESET) {
                    if (!$player->isSneaking()) {
                        if ($player->getBalance() >= $this->getPrice($line->getLine(3))) {
                            if ($player->getInventory()->canAddItem($this->getItem($line->getLine(2)))) {
                                $player->reduceBalance($this->getPrice($line->getLine(3)));
                                $item = $this->getItem($line->getLine(2));
                                $player->getInventory()->addItem($item);
                                $player->sendMessage(TextFormat::GREEN . "Successfully purchased x" . $item->getCount() . " " . $item->getName() . "!");
                                $this->sendSellText($player, $block, TextFormat::GREEN . "Bought\n". $item->getName() . TextFormat::EOL . TextFormat::GREEN . "for\n" . $line->getLine(3), join("\n", $tile->getText()->getLines()));
                            } else {
                                $player->sendMessage(TextFormat::RED . "Your inventory is full!");
                            }
                        } else {
                            $player->sendMessage(TextFormat::RED . "You do not have enough money to make this purchase.");
                        }
                    }
                } elseif ($line->getLine(0) == TextFormat::RED . "~SELL~" . TextFormat::RESET) {
                    if (!$player->isSneaking()) {
                        $item = $this->getItem($line->getLine(2));
                        if ($player->getItemCount($item) >= $item->getCount()) {
                            $player->getInventory()->removeItem($item);
                            $player->addBalance($this->getPrice($line->getLine(3)));
                            $player->sendMessage(TextFormat::GREEN . "Sold {$item->getCount()}x " . $item->getName() . " for " . $this->getPrice($line->getLine(3)));
                            $this->sendSellText($player, $block, TextFormat::GREEN . "Sold \n{$item->getName()}\n" . TextFormat::GREEN . "for\n" . $line->getLine(3), join("\n", $tile->getText()->getLines()));
                        } else {
                            $player->sendMessage(TextFormat::RED . "You do not have the required items in your inventory.");
                        }
                    }
                }
            }
        }
    }

    public function onCombatLoggerDamage(EntityDamageEvent $event): void
    {
        $logger = $event->getEntity();
        if ($logger instanceof CombatLogger && $event instanceof EntityDamageByEntityEvent) {
            $attacker = $event->getDamager();
            if ($attacker instanceof HCFPlayer) {
                if ($logger->getFaction() !== null && HCF::getInstance()->getFactionManager()->equalFaction($attacker->getFaction(), $logger->getFaction())) {
                    $event->cancel();
                } else {
                    $logger->lastDamager = $attacker;
                }
            }
        }
    }

    public function getItem(string $text): Item
    {
        $values = explode(":", $text);
        return ItemFactory::getInstance()->get($values[0], $values[1], $values[2]);
    }

    public function getPrice(string $text): int
    {
        return str_replace("$", "", $text);
    }

    public function getSafestUpSignBlock(Block $block): ?Vector3
    {
        $vector = null;
        for ($i = $block->getPosition()->getFloorY() + 1; $i < 256; $i++) {
            if ($block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ()))->getId() == 0 and $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i + 1, $block->getPosition()->getZ()))->getId() == 0) {
                $vector = new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ());
                break;
            }
        }
        return $vector;
    }

    public function getSafestDownSignBlock(Block $block): ?Vector3
    {
        $vector = null;
        for ($i = $block->getPosition()->getFloorY() - 1; $i > 0; $i--) {
            if ($block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ()))->getId() == 0 and $block->getPosition()->getWorld()->getBlock(new Vector3($block->getPosition()->getX(), $i + 1, $block->getPosition()->getZ()))->getId() == 0) {
                $vector = new Vector3($block->getPosition()->getX(), $i, $block->getPosition()->getZ());
                break;
            }
        }
        return $vector;
    }


    protected function sendSellText(Player $player, Block $block, string $next_text, string $last_text): void
    {
        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new class($player, $block, $next_text) extends Task {
            private HCFPlayer $player;
            private Block $block;
            private string $text;

            public function __construct(HCFPlayer $player, Block $block, string $text)
            {
                $this->player = $player;
                $this->block = $block;
                $this->text = $text;
            }

            public function onRun(): void
            {
                if ($this->player->isOnline() === false) {
                    return;
                }
                $pk = new BlockActorDataPacket();
                $tile = $this->block->getPosition()->getWorld()->getTile($this->block->getPosition());
                $pk->blockPosition = new BlockPosition($this->block->getPosition()->x, $this->block->getPosition()->getY(), $this->block->getPosition()->z);
                if ($tile instanceof Sign) {
                    $nbt = $tile->getSpawnCompound();
                    $nbt->setTag(Sign::TAG_TEXT_BLOB, new StringTag($this->text));
                    $pk->nbt = new CacheableNbt($nbt);
                    $this->player->getNetworkSession()->sendDataPacket($pk);
                }
            }
        }, 15);
        HCF::getInstance()->getScheduler()->scheduleDelayedTask(new class($player, $block, $last_text) extends Task {
            private HCFPlayer $player;
            private Block $block;
            private string $text;

            public function __construct(HCFPlayer $player, Block $block, string $text)
            {
                $this->player = $player;
                $this->block = $block;
                $this->text = $text;
            }

            public function onRun(): void
            {
                if ($this->player->isOnline() === false) {
                    return;
                }
                $pk = new BlockActorDataPacket();
                $tile = $this->block->getPosition()->getWorld()->getTile($this->block->getPosition());
                $pk->blockPosition = new BlockPosition($this->block->getPosition()->x, $this->block->getPosition()->getY(), $this->block->getPosition()->z);
                if ($tile instanceof Sign) {
                    $nbt = $tile->getSpawnCompound();
                    $nbt->setTag(Sign::TAG_TEXT_BLOB, new StringTag($this->text));
                    $pk->nbt = new CacheableNbt($nbt);
                    $this->player->getNetworkSession()->sendDataPacket($pk);
                }
            }
        }, 25);
    }
}