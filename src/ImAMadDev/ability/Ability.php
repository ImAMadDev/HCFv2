<?php

namespace ImAMadDev\ability;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use pocketmine\block\Thin;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

abstract class Ability {

	/** @var string */
	private string $name;
	
	public const ABILITY = "Ability";
	
	public const INTERACT_ABILITY = "Interaction";

    public const PLACE_ABILITY = "Place";
	
	public const DAMAGE_ABILITY = "Damageable";

    public int $cooldown = 0;

    /**
     * @param int $count
     * @param mixed|null $value
     * @return Item
     */
	abstract public function get(int $count = 1, mixed $value = null): Item;

	/**
	 * @return string
	 */
	abstract public function getName(): string;
	
	
	abstract public function getColoredName(): string;
	
	
	abstract public function isAbility(Item $item): bool;

    public function handCountdown(HCFPlayer|Player $player) : string
    {
        if ($this->cooldown == 0) return "";
        if ($player->getCache()->hasCountdown($this->getName(), ($this->cooldown / 2))){
            if((($this->cooldown / 2) - (time() - $player->getCache()->getCountdown($this->getName()))) > 0){
                $c = $this->cooldown / 2;
                $time = ($c - (time() - $player->getCache()->getCountdown($this->getName())));
                return TextFormat::GRAY . "Time left: " . TextFormat::LIGHT_PURPLE . gmdate('i:s', $time);
            }
        } elseif ($player->getCooldown()->has($this->getName())){
            return TextFormat::GRAY. "Time left: " . TextFormat::LIGHT_PURPLE . gmdate('i:s', $player->getCooldown()->get($this->getName()));
        }
        return "";
    }

}