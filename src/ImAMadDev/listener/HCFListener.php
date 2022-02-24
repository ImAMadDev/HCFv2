<?php

namespace ImAMadDev\listener;

use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\customenchants\CustomEnchantments;
use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;
use ImAMadDev\player\{HCFPlayer, PlayerCache, PlayerUtils, PlayerData};
use ImAMadDev\ticks\asynctask\SaveSkinAsyncTask;
use ImAMadDev\ticks\player\Scoreboard;
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
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
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
    NetworkChunkPublisherUpdatePacket,
    types\BlockPosition,
    types\CacheableNbt,
    types\inventory\ItemStackWrapper};
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\World;

class HCFListener implements Listener
{

    public function handleLeaves(LeavesDecayEvent $event): void
    {
        $event->cancel();
    }

    public function handleGrow(BlockGrowEvent $event): void
    {
        $block = $event->getBlock();
        if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::KOTH ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::ROAD ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::WARZONE
        ) {
            $event->cancel();
        }
    }

    public function handleBurn(BlockBurnEvent $event): void
    {
        $block = $event->getBlock();
        if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::KOTH ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::ROAD ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::WARZONE
        ) {
            $event->cancel();
        }
    }

    public function handleSpread(BlockSpreadEvent $event): void
    {
        $block = $event->getBlock();
        if (ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::SPAWN ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::KOTH ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::ROAD ||
            ClaimManager::getInstance()->getClaimByPosition($block->getPosition())?->getClaimType()->getType() == ClaimType::WARZONE
        ) {
            $event->cancel();
        }
    }

    public function handleItemSpawn(ItemSpawnEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof ItemEntity) return;
        if (ClaimManager::getInstance()->getClaimByPosition($entity->getPosition()->asPosition())?->getClaimType()->getType() == ClaimType::SPAWN and SOTWManager::isEnabled()) {
            $player = $entity->getOwningEntity();
            if ($player instanceof HCFPlayer) {
                $player->sendMessage(TextFormat::RED . "In the SOTW items dropped in the spawn will be removed automatically.");
            }
            $entity->flagForDespawn();
        }
    }

    public function onSignChange(SignChangeEvent $event): void
    {
        if (strtolower($event->getNewText()->getLine(0)) == "[elevator]") {
            if (strtolower($event->getNewText()->getLine(1)) == "up" || strtolower($event->getNewText()->getLine(1)) == "down") {
                $event->setNewText(new SignText([
                    0 => TextFormat::RED . "[Elevator]" . TextFormat::RESET,
                    1 => strtolower($event->getNewText()->getLine(1)),
                    2 => "",
                    3 => ""
                ]));
                $event->getPlayer()->sendMessage(TextFormat::GRAY . "You have created an elevator and remember that there must be 2 non-solid blocks for the elevator to work.!");
            }
        }
    }

    public function onSignShopChange(SignChangeEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player->hasPermission("configurate.shop") && $player->getGamemode() !== GameMode::CREATIVE()) return;
        if (is_string($event->getSign()->getPickedItem(true)->getCustomBlockData()?->getTag(Sign::TAG_TEXT_BLOB)?->getValue())) {
            $text = $event->getSign()->getPickedItem(true)->getCustomBlockData()?->getTag(Sign::TAG_TEXT_BLOB)?->getValue();
            if (str_starts_with(TextFormat::clean(explode("\n", $text)[1]), "slain")) {
                $event->cancel();
                return;
            }
        }
        if (strtolower($event->getNewText()->getLine(0)) == "[shop]" && $player->hasPermission("configurate.shop")) {
            if (strtolower($event->getNewText()->getLine(1)) == "buy") {
                $items = explode(":", $event->getNewText()->getLine(2));
                if (count($items) === 3) {
                    if ($items[2] >= 1) {
                        $item = ItemFactory::getInstance()->get((int)$items[0], (int)$items[1], (int)$items[2]);
                        $name = $item->getName() == "Enchanted Golden Apple" ? "Gapple" : $item->getName();
                        $event->setNewText(new SignText([
                            0 => TextFormat::GREEN . "~" . strtoupper($event->getNewText()->getLine(1)) . "~" . TextFormat::RESET,
                            1 => str_replace(["Lazuli ", "Stained "], "", $name),
                            2 => $item->getId() . ":" . $item->getMeta() . ":" . $item->getCount(),
                            3 => "$" . $event->getNewText()->getLine(3)
                        ]));
                    }
                }
            } elseif (strtolower($event->getNewText()->getLine(1)) == "sell" && $player->hasPermission("configurate.shop")) {
                $items = explode(":", $event->getNewText()->getLine(2));
                if (count($items) === 3) {
                    if ($items[2] >= 1) {
                        $item = ItemFactory::getInstance()->get((int)$items[0], (int)$items[1], (int)$items[2]);
                        $name = $item->getName() == "Enchanted Golden Apple" ? "Gapple" : $item->getName();
                        $event->setNewText(new SignText([
                            0 => TextFormat::RED . "~" . strtoupper($event->getNewText()->getLine(1)) . "~" . TextFormat::RESET,
                            1 => str_replace(["Lazuli ", "Stained "], "", $name),
                            2 => $item->getId() . ":" . $item->getMeta() . ":" . $item->getCount(),
                            3 => "$" . $event->getNewText()->getLine(3)
                        ]));
                    }
                }
            }
        }
    }

    /**
     * @priority MONITOR
     * @param BlockBreakEvent $event
     * @return void
     */
    public function handleBreak(BlockBreakEvent $event): void
    {
        if ($event->getBlock() instanceof BaseSign) {
            if (is_string($event->getBlock()->getPickedItem(true)->getCustomBlockData()?->getTag(Sign::TAG_TEXT_BLOB)?->getValue())) {
                $text = $event->getBlock()->getPickedItem(true)->getCustomBlockData()?->getTag(Sign::TAG_TEXT_BLOB)?->getValue();
                if (str_starts_with(TextFormat::clean(explode("\n", $text)[1]), "slain")) {
                    $event->setDropsVariadic($event->getBlock()->getPickedItem(true)->setCustomName(TextFormat::DARK_PURPLE . "Death Sing " . TextFormat::clean(explode("\n", $text)[0]))->setLore([]));
                }
            }
        }
    }

    /*public function onInventoryCloseEvent(InventoryCloseEvent $event) : void {
        $player = $event->getPlayer();
        $inventory = $event->getInventory();
        if ($player instanceof HCFPlayer) {
            if ($inventory instanceof EnderChestInventory) {
                # This is to remove the chest when the player closes the inventory
                $position = $inventory->getHolder();

                if ($player->getReplaceableBlock() !== 0) {
                    $blockRuntimeId = $player->getReplaceableBlock();
                } else {
                    $blockRuntimeId = RuntimeBlockMapping::getInstance()->fromRuntimeId(BlockFactory::getInstance()->get(BlockLegacyIds::BEDROCK, 0)->getFullId());
                }
                $pk = UpdateBlockPacket::create(new BlockPosition($position->x, $position->y, $position->z), $blockRuntimeId, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
                $player->getNetworkSession()->sendDataPacket($pk);
            }
        }
    }*/

    public function onCraft(CraftItemEvent $event): void
    {
        $outputs = $event->getOutputs();
        foreach ($outputs as $output) {
            if ($output->getId() == BlockLegacyIds::BREWING_STAND_BLOCK or $output->getId() == BlockLegacyIds::TNT) {
                $event->cancel();
                $event->getPlayer()->sendMessage(TextFormat::RED . "You cant craft this item");
            }
        }
    }
}