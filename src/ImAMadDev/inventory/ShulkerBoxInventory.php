<?php

namespace ImAMadDev\inventory;

use ImAMadDev\tile\ShulkerBox;
use pocketmine\inventory\ContainerInventory;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class ShulkerBoxInventory extends ContainerInventory
{

  /**
   * @var ShulkerBox
   */
    protected $holder;

    public function __construct(ShulkerBox $tile)
    {
        parent::__construct($tile);
    }

    public function getName(): string
    {
        return "Shulker Box";
    }
    public function getDefaultSize(): int
    {
        return 27;
    }

    public function getNetworkType(): int
    {
        return WindowTypes::CONTAINER;
    }

    /**
     * @param Player $who
     */
    public function onOpen(Player $who): void
    {
        parent::onOpen($who);
        if (count($this->getViewers()) === 1 && ($level = $this->getHolder()->getWorld()) instanceof Level) {
            $this->broadcastBlockEventPacket($this->getHolder(), true);
            $level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_SHULKERBOX_OPEN);
        }
    }

    /**
     * @return  ShulkerBox
     */
    public function getHolder()
    {
        return $this->holder;
    }

    protected function broadcastBlockEventPacket(Vector3 $vector, bool $isOpen)
    {
        $pk = new BlockEventPacket();
        $pk->x = (int)$vector->x;
        $pk->y = (int)$vector->y;
        $pk->z = (int)$vector->z;
        $pk->eventType = 1;
        $pk->eventData = $isOpen ? 1 : 0;
        $this->getHolder()->getWorld()->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
    }

    /**
     * @param Player $who
     */
    public function onClose(Player $who): void
    {
        if (count($this->getViewers()) === 1 && ($level = $this->getHolder()->getWorld()) instanceof Level) {
            $this->broadcastBlockEventPacket($this->getHolder(), false);
            $level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_SHULKERBOX_CLOSED);
        }
        parent::onClose($who);
    }
}
