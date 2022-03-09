<?php

namespace ImAMadDev\shop\tasks;

use ImAMadDev\player\HCFPlayer;
use pocketmine\block\tile\Sign;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;

class SendFakeTextTask extends Task
{

    private HCFPlayer $player;
    private Position $pos;
    private string $text;

    public function __construct(HCFPlayer $player, Position $pos, string $text)
    {
        $this->player = $player;
        $this->pos = $pos;
        $this->text = $text;
    }

    public function onRun(): void
    {
        if ($this->getPlayer()->isConnected() === false) {
            return;
        }
        $pk = new BlockActorDataPacket();
        $tile = $this->getPos()->getWorld()->getTile($this->getPos());
        $pk->blockPosition = new BlockPosition($this->getPos()->x, $this->getPos()->getY(), $this->getPos()->z);
        if ($tile instanceof Sign) {
            $nbt = $tile->getSpawnCompound();
            $nbt->setTag(Sign::TAG_TEXT_BLOB, new StringTag($this->getText()));
            $pk->nbt = new CacheableNbt($nbt);
            $this->getPlayer()->getNetworkSession()->sendDataPacket($pk);
        }
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

    /**
     * @return Position
     */
    public function getPos(): Position
    {
        return $this->pos;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}