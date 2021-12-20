<?php

namespace ImAMadDev\event;

use JetBrains\PhpStorm\Pure;
use pocketmine\block\Block;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;
use pocketmine\world\Position;

class PlayerCreateNetherPortalEvent extends BlockEvent implements Cancellable
{
    use CancellableTrait;

    private Player $player;

    /**
     * @param Player $player
     * @param Block $block
     */
    #[Pure] public function __construct(Player $player, Block $block)
    {
        $this->player = $player;
        parent::__construct($block);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}