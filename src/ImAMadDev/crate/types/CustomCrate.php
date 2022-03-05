<?php

namespace ImAMadDev\crate\types;

use ImAMadDev\crate\Crate;
use ImAMadDev\player\HCFPlayer;
use JetBrains\PhpStorm\Pure;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class CustomCrate extends Crate
{

    /** @var string */
    private string $name;

    /** @var Item[] */
    private array $contents;

    /**
     * @var string
     */
    public static string $CUSTOM_KEY;

    /**
     * @var string
     */
    public string $customName;

    /**
     * @var Item
     */
    private Item $key;

    /**
     * @var Item
     */
    private Item $down_block;

    /**
     * @param string $name
     * @param array $contents
     * @param string $customName
     * @param Item $key
     * @param Item $down_block
     */
    public function __construct(string $name, array $contents, string $customName, Item $key, Item $down_block)
    {
        $this->name = $name;
        $this->contents = $contents;
        self::$CUSTOM_KEY = $name . "Key";
        $this->customName = TextFormat::colorize($customName);
        $this->key = $key;
        $this->down_block = $down_block;
    }

    /**
     * @return array
     */
    public function getInventory(): array
    {
        return $this->contents;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getColoredName(): string
    {
        return $this->customName;
    }

    /**
     * @param Block $block
     * @return bool
     */
    public function isCrate(Block $block): bool
    {
        if(in_array($block->getId(), self::BLOCK_CRATES)) {
            if($block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getId() == $this->down_block->getId() &&
                $block->getPosition()->getWorld()->getBlockAt($block->getPosition()->getFloorX(), $block->getPosition()->getFloorY() - 4, $block->getPosition()->getFloorZ())->getMeta() == $this->down_block->getMeta()){
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    #[Pure] public function isCrateName(string $name) : bool {
        return strtolower($this->getName()) == strtolower($name);
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function isCrateKey(Item $item) : bool {
        if($item->getId() === $this->key->getId() && $item->getMeta() === $this->key->getMeta() && $item->getNamedTag()->getTag(self::$CUSTOM_KEY) instanceof CompoundTag) {
            return true;
        }
        return false;
    }

    public function getCrateKey(int $count = 1): Item {
        $item = ItemFactory::getInstance()->get($this->key->getId(), $this->key->getMeta(), $count);
        $item->getNamedTag()->setTag(self::KEY_TAG, CompoundTag::create());
        $item->getNamedTag()->setTag(self::$CUSTOM_KEY, CompoundTag::create());
        $item->setCustomName($this->getColoredName() . " KEY");
        $item->setLore([TextFormat::GRAY . "You can redeem this key at " . $this->getColoredName() . " Crate", TextFormat::BOLD . TextFormat::YELLOW . " * " . TextFormat::RESET . TextFormat::GRAY . "Right-Click Crate with key to redeem!"]);
        $item->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
        return $item;
    }

    public function getContents(HCFPlayer|Player $player) : void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->setName($this->getColoredName() . " " . TextFormat::GREEN . "Crate Content");
        $menu->setListener(function(InvMenuTransaction $transaction) : InvMenuTransactionResult{
            return $transaction->discard();
        });
        $menu->send($player);
        $menu->getInventory()->setContents($this->getInventory());
        for ($i = 0; $i < $menu->getInventory()->getSize(); $i++){
            if ($menu->getInventory()->getItem($i)->getId() == BlockLegacyIds::AIR){
                $panel = ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS_PANE, 14);
                $panel->setCustomName(TextFormat::RED);
                $menu->getInventory()->setItem($i, $panel);
            }
        }
    }

    public function open(HCFPlayer|Player $player, Block $block) : void {
        $status = $player->getInventoryStatus();
        if($status === "FULL") {
            $player->sendBack($block->getPosition()->asVector3(), 1);
        } else {
            $items = [];
            foreach($this->getInventory() as $item) {
                $items[] = $item;
            }
            $win = $items[array_rand($items)];
            $name = $win->hasCustomName() === true ? $win->getCustomName() : $win->getName();
            $player->getInventory()->addItem($win);
            $item = $player->getInventory()->getItemInHand();
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : ItemFactory::getInstance()->get(BlockLegacyIds::AIR));
            $player->sendMessage(TextFormat::YELLOW . "You have received: " . TextFormat::GREEN . TextFormat::BOLD . $name);
        }
    }
}