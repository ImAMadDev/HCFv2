<?php

namespace ImAMadDev\player\sessions;

use ImAMadDev\player\HCFPlayer;
use pocketmine\world\Position;

class ClaimSession
{

    private ?Position $position_1 = null, $position_2 = null;


    public function __construct(
        private string $name,
        private string $type,
        private bool $opClaim,
        private HCFPlayer $player
    ){}

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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Position|null
     */
    public function getPosition1(): ?Position
    {
        return $this->position_1;
    }

    /**
     * @param Position $position_1
     */
    public function setPosition1(Position $position_1): void
    {
        $this->position_1 = $position_1;
        $this->player->sendFakeBlock($position_1);
    }

    /**
     * @return Position|null
     */
    public function getPosition2(): ?Position
    {
        return $this->position_2;
    }

    /**
     * @param Position $position_2
     */
    public function setPosition2(Position $position_2): void
    {
        $this->position_2 = $position_2;
        $this->player->sendFakeBlock($position_2);
    }

    /**
     * @return bool
     */
    public function isOpClaim(): bool
    {
        return $this->opClaim;
    }

    /**
     * @return HCFPlayer
     */
    public function getPlayer(): HCFPlayer
    {
        return $this->player;
    }

}