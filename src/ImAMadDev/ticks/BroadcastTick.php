<?php

namespace ImAMadDev\ticks;

use ImAMadDev\HCF;
use JetBrains\PhpStorm\Pure;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class BroadcastTick extends Task {
	
	private HCF $main;
	
	private int $currentId = 0; 
	
	private array $messages = ["&6&l[?]&r Do you want to buy a rank? Visit our store: &6https://pacmanlife.buycraft.net/",
		"&6&l[?]&r You can enter our discord through this link: &9https://discord.gg/r483mW5z",
		"&6&l[?]&r You can see all the faction commands with &e/f help",
		"&6&l[?]&r Did you know that by voting for our server you get &arewards&r? Vote using your name at: &6https://bit.ly/3z1Jrbl",
		"&6&l[?]&r Highest Roll Players Online: &6{roll}",
		"&4&ll[!]&r This is a BETA 2.0 version of &dMine&fStalia&r, if you find any problem like a bug or glitch, please contact us at &9https://discord.gg/r483mW5z"
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
		$this->main->getServer()->broadcastMessage(TextFormat::colorize($this->getNextMessage()));
	}
	
	#[Pure] private function getAllVIPPlayers() : string {
		$players = [];
		foreach($this->main->getServer()->getOnlinePlayers() as $player) {
			if($player->hasRank("Anubis")) $players[] = $player->getName();
		}
		return implode(", ", $players);
	}
}