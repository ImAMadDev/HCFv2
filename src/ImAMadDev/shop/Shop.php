<?php

namespace ImAMadDev\shop;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\shop\tasks\SendFakeTextTask;
use pocketmine\block\Block;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\WorldException;

class Shop
{
    const SELL = "sell_shop";
    const BUY = "buy_shop";

    private string $name;
    private int $price;
    private int $quantity;
    private Item $item;
    private Position $position;
    private string $mode;

    public function __construct(Item $item, string $name, int $quantity, int $price, Position $position, string $mode = self::SELL)
    {
        $this->item = $item;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->mode = $mode;
        $this->price = $price;
        $this->position = $position;
    }

    public function getText(): SignText
    {
        return new SignText([
            "&a&l[SHOP]",
            $this->getName(),
            $this->getQuantity(),
            $this->getPrice()
        ]);
    }

    public function getStringText(): string
    {
        return $this->mode == self::BUY ? TextFormat::colorize("&aBought\n&6". $this->getName() . "\n&afor\n&6" . $this->getPrice()) : TextFormat::colorize("&aSold\n&6". $this->getName() . "\n&afor\n&6" . $this->getPrice());
    }

    public static function parseItem(string $item): Item
    {
        $values = explode(":", $item);
        return ItemFactory::getInstance()->get($values[0], $values[1], $values[2]);
    }

    public static function parsePosition(string $position): Position
    {
        $values = explode(":", $position);
        try {
            $world = Server::getInstance()->getWorldManager()->getWorldByName($values[3]);
        } catch (WorldException $exception){
            throw new WorldException("World doesnt exists: $values[3]");
        }
        return new Position($values[0], $values[1], $values[2], $world);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    public function isHere(Position $position): bool
    {
        return ($this->getPosition()->equals($position));
    }

    public function click(HCFPlayer $player): void
    {
        if (!$player->isSneaking()) {
            if ($player->getBalance() >= $this->getPrice()) {
                if ($player->getInventory()->canAddItem($this->getItem())) {
                    $player->reduceBalance($this->getPrice());
                    $item = $this->getItem();
                    $player->getInventory()->addItem($item);
                    $player->sendMessage(TextFormat::GREEN . "Successfully ". ($this->mode == self::BUY ? "Purchased" : "Sold") ." x" . $item->getCount() . " " . $this->getName() . "!");
                    HCF::getInstance()->getScheduler()->scheduleDelayedTask(new SendFakeTextTask($player, $this->position, $this->getStringText()), 15);
                    HCF::getInstance()->getScheduler()->scheduleDelayedTask(new SendFakeTextTask($player, $this->position, join("\n", $this->getText()->getLines())), 25);
                } else {
                    $player->sendMessage(TextFormat::RED . "Your inventory is full!");
                }
            } else {
                $player->sendMessage(TextFormat::RED . "You do not have enough money to make this purchase.");
            }
        }
    }

}