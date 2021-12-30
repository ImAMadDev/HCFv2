<?php

namespace ImAMadDev\ticks;

use ImAMadDev\HCF;
use JetBrains\PhpStorm\Pure;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ImAMadDev\ticks\player\UpdateDataAsyncTask;

class BroadcastTick extends Task {
	
	private HCF $main;
	
	private int $currentId = 0; 
	
	private array $messages = ["&6&l[?]&r Do you want to buy a rank? Visit our store: &6www.minestalia.com/shop",
		"&6&l[?]&r You can enter our discord through this link: &9www.minestalia.com/discord",
		"&6&l[?]&r You can see all the faction commands with &e/f help",
		"&6&l[?]&r Did you know that by voting for our server you get &arewards&r? Vote using your name at: &6www.minestalia.com/vote",
		"&6&l[?]&r Highest Roll Players Online: &6{roll}",
		"&4&ll[!]&r This is a BETA 2.0 version of &dMine&fStalia&r, if you find any problem like a bug or glitch, please contact us at &9www.minestalia.com/discord"
	];

	public function __construct(HCF $main) {
		$this->main = $main;
	}
	
	public function getNextMessage(): string {
		if(isset($this->messages[$this->currentId])) {
			$message = $this->messages[$this->currentId];
			$this->currentId++;
			return str_replace("{roll}", $this->getAllVIPPlayers(), $message);
		}
		$this->currentId = 0;
		return str_replace("{roll}", $this->getAllVIPPlayers(), $this->messages[$this->currentId]);
	}
	
	public function onRun(): void {
		$this->main->getServer()->broadcastMessage(TextFormat::EOL . TextFormat::colorize($this->getNextMessage()) . TextFormat::EOL);
        Server::getInstance()->getAsyncPool()->submitTask(new UpdateDataAsyncTask());
	}
	
	#[Pure] private function getAllVIPPlayers() : string {
		$players = [];
		foreach($this->main->getServer()->getOnlinePlayers() as $player) {
			if($player->hasRank("Anubis")) $players[] = $player->getName();
		}
		return implode(", ", $players);
	}
}