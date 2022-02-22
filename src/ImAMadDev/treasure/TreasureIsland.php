<?php

declare(strict_types=1);

namespace ImAMadDev\treasure;

use pocketmine\player\GameMode;
use pocketmine\world\Position;
use pocketmine\Server;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\utils\HCFUtils;
use ImAMadDev\HCF;
use ImAMadDev\utils\DiscordIntegration;
use ImAMadDev\faction\Faction;
use ImAMadDev\koth\ticks\AutomaticKOTHTick;
use ImAMadDev\manager\CrateManager;
use pocketmine\utils\TextFormat;

class TreasureIsland
{
	
	public ?string $name = null;
	
	public array $pos = ["one" => null, "two" => null];
	
	public int $resetTime = 300;
	
	public int $defaultTime = 300;

	public array $items = [];

    private string $world;

    public function __construct(string $name, string $pos1, string $pos2, int $time, string $world, array $items = []) {
		$this->name = $name;
		$this->pos["one"] = $pos1;
		$this->pos["two"] = $pos2;
		$this->resetTime = $time;
		$this->defaultTime = $time;
		$this->world = $world;
		$this->items = $items;
	}
	
	public function getTime(): int{
		return $this->resetTime;
	}
	
	public function getDefaultTime(): int{
		return $this->defaultTime;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function getServer(): Server{
		return Server::getInstance();
	}
	
	public function getPos(string $pos): Position {
		$vec = explode(":", $this->pos[$pos]);
		$level = $this->getServer()->getWorldManager()->getWorldByName($this->world);
		return new Position($vec[0], $vec[1], $vec[2], $level);
	}
    
    public function isHere(Position $position): bool {
		$world = $position->getWorld();
		$firstPosition = $this->getPos("one");
		$secondPosition = $this->getPos("two");
		$minX = min($firstPosition->getX(), $secondPosition->getX());
		$maxX = max($firstPosition->getX(), $secondPosition->getX());
		$minZ = min($firstPosition->getZ(), $secondPosition->getZ());
		$maxZ = max($firstPosition->getZ(), $secondPosition->getZ());
		return $minX <= $position->getX() and $maxX >= $position->getFloorX() and $minZ <= $position->getZ() and $maxZ >= $position->getFloorZ() and
            $this->world === $world->getFolderName();
	}
	
	public function enable(): void{
		$this->enable = true;
	}
	
	public function disable(): void{
		$this->enable = false;
	}
	
	public function isEnabled(): bool{
		return $this->enable;
	}
	
	public function onTick(): void{
		if($this->isEnabled()){
			if($this->canDecrease()){
				--$this->time;
				$this->chooseKing();
				if(($this->time % 60) === 0){
					$this->getServer()->broadcastMessage(TextFormat::GOLD . $this->king->getName() . TextFormat::GRAY . " is capturing the " . TextFormat::BOLD . TextFormat::DARK_RED . "KOTH " . $this->getName() . TextFormat::RESET . TextFormat::GRAY . " during " . TextFormat::GOLD . gmdate("i:s", $this->time));
				}
				if($this->time <= 0){
					$this->giveKeys();
					$this->getServer()->broadcastMessage("§e[§6TeasureIsland§e] §r" . TextFormat::GOLD . $this->getName() . TextFormat::GRAY . " has reloaded the " . TextFormat::BOLD . TextFormat::DARK_RED . "KOTH ".$this->getName() . TextFormat::RESET . TextFormat::GRAY . " during " . TextFormat::GOLD . gmdate("i:s", $this->getDefaultTime()));
					$this->reset();
				}
			}
		}
	}
}