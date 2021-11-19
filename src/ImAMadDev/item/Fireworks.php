<?php


namespace ImAMadDev\item;

use ImAMadDev\entity\projectile\FireworksRocket;

use pocketmine\block\Block;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;

class Fireworks extends Item {

    public const TYPE_SMALL_SPHERE = 0;
    public const TYPE_HUGE_SPHERE = 1;
    public const TYPE_STAR = 2;
    public const TYPE_CREEPER_HEAD = 3;
    public const TYPE_BURST = 4;

    //color = chr(dye metadata)
    public const COLOR_BLACK = "\x00";
    public const COLOR_RED = "\x01";
    public const COLOR_DARK_GREEN = "\x02";
    public const COLOR_BROWN = "\x03";
    public const COLOR_BLUE = "\x04";
    public const COLOR_DARK_PURPLE = "\x05";
    public const COLOR_DARK_AQUA = "\x06";
    public const COLOR_GRAY = "\x07";
    public const COLOR_DARK_GRAY = "\x08";
    public const COLOR_PINK = "\x09";
    public const COLOR_GREEN = "\x0a";
    public const COLOR_YELLOW = "\x0b";
    public const COLOR_LIGHT_AQUA = "\x0c";
    public const COLOR_DARK_PINK = "\x0d";
    public const COLOR_GOLD = "\x0e";
    public const COLOR_WHITE = "\x0f";

    public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
        parent::__construct($identifier, $name);
    }

    public function getRandomType() : int
    {
        return match (rand(0, 5)){
            1 => self::TYPE_HUGE_SPHERE,
            2 => self::TYPE_STAR,
            3 => self::TYPE_CREEPER_HEAD,
            4 => self::TYPE_BURST,
            default => self::TYPE_SMALL_SPHERE
        };
    }

    public function getRandomColor() : string
    {
        return match (rand(0, 15)){
            1 => self::COLOR_RED,
            2 => self::COLOR_DARK_GREEN,
            3 => self::COLOR_BROWN,
            4 => self::COLOR_BLUE,
            5 => self::COLOR_DARK_PURPLE,
            6 => self::COLOR_DARK_AQUA,
            7 => self::COLOR_GRAY,
            8 => self::COLOR_DARK_GRAY,
            9 => self::COLOR_PINK,
            10 => self::COLOR_GREEN,
            11 => self::COLOR_YELLOW,
            12 => self::COLOR_LIGHT_AQUA,
            13 => self::COLOR_DARK_PINK,
            14 => self::COLOR_GOLD,
            15 => self::COLOR_WHITE,
            default => self::COLOR_BLACK
        };
    }

    public function getFlightDuration(): int {
        return $this->getExplosionsTag()->getByte("Flight", 1);
    }

    public function getRandomizedFlightDuration(): int {
        return ($this->getFlightDuration() + 1) * 10 + mt_rand(0, 5) + mt_rand(0, 6);
    }

    public function setFlightDuration(int $duration): void {
        $this->getExplosionsTag()->setByte("Flight", $duration);
    }

    public function addExplosion(int $type, string $color, string $fade = "", bool $flicker = false, bool $trail = false): void
    {
        $tag = $this->getExplosionsTag();
        $explosions = $tag->getListTag("Explosions");
        if($explosions === null){
            $tag->setTag("Explosions", $explosions = new ListTag());
        }

        $explosions->push(CompoundTag::create()
            ->setByte("FireworkType", $type)
            ->setByteArray("FireworkColor", $color)
            ->setByteArray("FireworkFade", $fade)
            ->setByte("FireworkFlicker", $flicker ? 1 : 0)
            ->setByte("FireworkTrail", $trail ? 1 : 0)
        );
    }

    protected function getExplosionsTag(): CompoundTag
    {
        $tag = $this->getNamedTag()->getCompoundTag("Fireworks");
        if ($tag === null) {
            $this->getNamedTag()->setTag("Fireworks", $tag = CompoundTag::create());
        }
        return $tag;
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult
    {
        $entity = new FireworksRocket(Location::fromObject($blockReplace->getPosition()->add(0.5, 0, 0.5), $player->getWorld(), lcg_value() * 360, 90), $this);

        $this->pop();
        $entity->spawnToAll();
        //TODO: what if the entity was marked for deletion?
        return ItemUseResult::SUCCESS();
    }
}