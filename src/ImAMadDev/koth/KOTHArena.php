<?php

declare(strict_types=1);

namespace ImAMadDev\koth;

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

class KOTHArena{
	
	public ?HCFPlayer $king = null;
	
	public ?string $name = null;
	
	public array $pos = ["one" => null, "two" => null];
	
	public array $corner = ["one" => null, "two" => null];
	
	public int $time = 300;

	public int $keys = 3;
	
	public int $defaultTime = 300;
	
	public bool $enable = false;

    private string $world;


    public function __construct(string $name, string $pos1, string $pos2, string $corner1, string $corner2, int $time, string $world, int $keys) {
		$this->name = $name;
		$this->pos["one"] = $pos1;
		$this->pos["two"] = $pos2;
		$this->corner["one"] = $corner1;
		$this->corner["two"] = $corner2;
		$this->time = $time;
		$this->world = $world;
		$this->keys = $keys;
		$this->defaultTime = $time;
	}
	
	public function getDefaultTime(): int{
		return $this->defaultTime;
	}
	
	public function getTime(): int{
		return $this->time;
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
	
	public function getCorner(string $corner): Position {
		$vec = explode(":", $this->corner[$corner]);
		$level = $this->getServer()->getWorldManager()->getWorldByName($this->world);
		return new Position($vec[0], $vec[1], $vec[2], $level);
	}
	
	public function isOnCorner(Position $position): bool {
		$world = $position->getWorld();
		$firstPosition = $this->getCorner("one");
		$secondPosition = $this->getCorner("two");
		$minX = min($firstPosition->getX(), $secondPosition->getX());
		$maxX = max($firstPosition->getX(), $secondPosition->getX());
		$minY = min($firstPosition->getY(), $secondPosition->getY());
		$maxY = max($firstPosition->getY(), $secondPosition->getY());
		$minZ = min($firstPosition->getZ(), $secondPosition->getZ());
		$maxZ = max($firstPosition->getZ(), $secondPosition->getZ());
		return $minX <= $position->getX() and $maxX >= $position->getFloorX() and $minY <= $position->getY() and
			$maxY >= $position->getY() and $minZ <= $position->getZ() and $maxZ >= $position->getFloorZ() and
			$this->world === $world->getFolderName();
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
		$fields = ["_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ KOTH {$this->getName()}", "_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ Event has been triggered :clock3:", "The player who claims the KOTH gets $this->keys Keys", "To see the coordinates use /koth info {$this->getName()}", "━━━━━━━━ ⇜ ━━━━━━━━", "IP: play.minestalia.com", "SHOP: minestalia.com"];
		DiscordIntegration::sendToDiscord("━━━━━━━━ ⇜ ━━━━━━━━", "<@&911434300841422918>", DiscordIntegration::KOTH_WEBHOOK, "StaliaBot", $fields);
		$this->getServer()->broadcastMessage("§aA new KOTH event has been triggered: §9".$this->getName());
	}
	
	public function disable(): void{
		$this->enable = false;
	}
	
	public function isEnabled(): bool{
		return $this->enable;
	}
	
	public function getPlayersInCorner(): array{
		$players = [];
		foreach($this->getServer()->getOnlinePlayers() as $p){
			if($p->getGamemode() === GameMode::SURVIVAL() && !$p->isInvincible() && $p->isAlive()){
				if($this->isOnCorner($p->getPosition()) && $p->getFaction() !== null){
					$players[] = $p;
				}
			}
		}
		return $players;
	}
	
	public function canDecrease(): bool{
		$facs = [];
		if(count($this->getPlayersInCorner()) === 0){
			$this->time = $this->getDefaultTime();
			return false;
		}
		if(count($this->getPlayersInCorner()) === 1) return true;
		foreach($this->getPlayersInCorner() as $corner){
			if(!in_array($corner->getFaction(), $facs)){
				$facs[] = $corner->getFaction();
			}
		}
		return (count($facs) >= 1);
	}
	
	public function chooseKing(): void{
		if($this->king !== null){
			if(!$this->king instanceof HCFPlayer){
				$this->king = null;
				$this->time = $this->getDefaultTime();
			}
			if(!$this->king->isOnline()){
				$this->king = null;
				$this->time = $this->getDefaultTime();
			}
		}
		if(count($this->getPlayersInCorner()) === 0){
			if($this->king !== null){
				$this->king = null;
				$this->time = $this->getDefaultTime();
			}
		}
		if(count($this->getPlayersInCorner()) === 1){
			if($this->king !== null){
				if($this->king->getName() !== $this->getPlayersInCorner()[0]->getName()){
					$this->time = $this->getDefaultTime();
					$this->king = $this->getPlayersInCorner()[0];
					$this->king->sendMessage(TextFormat::GRAY . "You are capturing the " . TextFormat::DARK_RED . TextFormat::BOLD . "KOTH " . $this->getName());
				}
			} else {
				$this->time = $this->getDefaultTime();
				$this->king = $this->getPlayersInCorner()[0];
				$this->king->sendMessage(TextFormat::GRAY . "You are capturing the " . TextFormat::DARK_RED . TextFormat::BOLD . "KOTH " . $this->getName());
			}
		}
		if(count($this->getPlayersInCorner()) >= 2){
			if($this->king == null){
				$this->time = $this->getDefaultTime();
				$this->king = $this->getPlayersInCorner()[array_rand($this->getPlayersInCorner())];
				$this->king->sendMessage(TextFormat::GRAY . "You are capturing the " . TextFormat::DARK_RED . TextFormat::BOLD ."KOTH " . $this->getName());
			}
		}
	}
	
	public function finish(): void{
		$this->time = $this->getDefaultTime();
		HCF::getInstance()->getKOTHManager()->selectArena();
		$this->disable();
		$time = date('l jS \of F Y h:i:s A', (time() + HCFUtils::KOTH_COOLDOWN));
		$fields = ["━━━━━━━━ ⇜ ━━━━━━━━", " KOTH", "The event will take place on $time  :clock3:", "The player who captures it will receive 3 KOTH keys.", "To see the coordinates use /koth info <NAME>", "IP: play.minestalia.com", "SHOP: minestalia.com", "━━━━━━━━ ⇜ ━━━━━━━━"];
		DiscordIntegration::sendToDiscord("━━━━━━━━ ⇜ ━━━━━━━━", "<@&911434300841422918>", DiscordIntegration::KOTH_WEBHOOK, "StaliaBot", $fields);
		new AutomaticKOTHTick(HCF::getInstance(), (20* HCFUtils::KOTH_COOLDOWN));
	}
	
	public function giveKeys(): void{
		$koth = CrateManager::getInstance()->getCrateByName('KOTH')->getCrateKey($this->keys);
		if($this->king->getInventory()->canAddItem($koth)){
			$this->king->getInventory()->addItem($koth);
		} else {
			$this->king->getWorld()->dropItem($this->king->getPosition()->asVector3(), $koth);
		}
		$this->king->sendMessage(TextFormat::GRAY . "You have won x".$this->keys." " . $koth->getCustomName());
		if(($faction = $this->king->getFaction()) instanceof Faction) {
			$faction->addPoints(5);
		}
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
					$fields = ["━━━━━━━━ ⇜ ━━━━━━━━", "   KOTH", "The player {$this->king->getName()} ", "has captured KOTH {$this->getName()}", "During {$this->getDefaultTime()} seconds and obtained 3 Keys KOTH", "IP: play.minestalia.com", "SHOP: minestalia.com", "━━━━━━━━ ⇜ ━━━━━━━━"];
					DiscordIntegration::sendToDiscord("━━━━━━━━ ⇜ ━━━━━━━━", "<@&911434300841422918>", DiscordIntegration::KOTH_WEBHOOK, "StaliaBot", $fields);
					$this->getServer()->broadcastMessage(TextFormat::GOLD . $this->king->getName() . TextFormat::GRAY . " has captured the " . TextFormat::BOLD . TextFormat::DARK_RED . "KOTH ".$this->getName() . TextFormat::RESET . TextFormat::GRAY . " during " . TextFormat::GOLD . gmdate("i:s", $this->getDefaultTime()));
					$this->finish();
				}
			}
		}
	}

}