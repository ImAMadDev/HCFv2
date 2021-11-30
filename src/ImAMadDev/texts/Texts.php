<?php

namespace ImAMadDev\texts;

use ImAMadDev\faction\FactionUtils;
use ImAMadDev\utils\HCFUtils;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\{World, Position};
use pocketmine\math\AxisAlignedBB;
use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use floatingtext\FloatingTextApi;

class Texts {
	
	public HCF $main;
	
	public array $data;
	
	public Position $position;
	
	public int $textID;
	
	public string $text;
	
	public array $send = [];
	
	public function __construct(HCF $main, array $data) {
		$this->main = $main;
		$this->data = $data;
		$world = $main->getServer()->getWorldManager()->getWorldByName($this->data['level']);
		$this->position = new Position($this->data['x'], $this->data['y'], $this->data['z'], $world);
		$this->textID = $data['id'];
		$this->text = $data['text'];
	}
	
	public function getName() : string {
		return $this->data['name'];
	}
	
	public function getWorldName() : string {
		return $this->data['level'];
	}
	
	public function getText() : string {
		return $this->text;
	}
/*
	public function onTick() : void {
		foreach($this->getNearByPlayers(60, 60) as $player) {
			if(!in_array($player->getName(), array_keys($this->send))) {
				$message = str_replace("{player}", $player->getName(), $this->getText());
				FloatingTextApi::sendText($this->textID, $player, $message);
				$this->send[$player->getName()] = $player;
			}
		}
		foreach($this->main->getServer()->getOnlinePlayers() as $player) {
			if(in_array($player->getName(), array_keys($this->send)) && round($player->distance($this->position)) > 200) {
				FloatingTextApi::removeText($this->textID, $player);
				unset($this->send[array_search($player->getName(), $this->send)]);
			}
		}
	}
	*/
	public function onTick() : void {
		foreach($this->main->getServer()->getOnlinePlayers() as $player) {
			if(!in_array($player->getName(), array_keys($this->send)) && stripos($player->getRegion(), "Spawn") !== false) {
				$message = str_replace("{player}", $player->getName(), $this->getText());
                $message = str_replace("{online}", count(Server::getInstance()->getOnlinePlayers()), $message);
                $message = str_replace("{map_information}", $this->getMapKit(), $message);
				FloatingTextApi::sendText($this->textID, $player, $message);
				$this->send[$player->getName()] = $player;
			}
		}
		foreach($this->send as $name => $player) {
			if($this->main->getServer()->getPlayerByPrefix($name) instanceof HCFPlayer) {
				continue;
			}
			unset($this->send[$name]);
		}
	}
	
	public function getNearbyPlayers(int $dictance, int $up) : array {
		$players = [];
		foreach($this->position->getWorld()->getNearbyEntities(new AxisAlignedBB($this->position->getFloorX() - $dictance, $this->position->getFloorY() - $up, $this->position->getFloorZ() - $dictance, $this->position->getFloorX() + $dictance, $this->position->getFloorY() + $up, $this->position->getFloorZ() + $dictance)) as $e){
			if($e instanceof HCFPlayer){
				$players[] = $e;
			}
		}
		return $players;
	}

    private function getMapKit() : string
    {
        return '&7&m--------------------------------' . TextFormat::EOL .
            '&6&lMapkit&7: &r' . TextFormat::EOL .
            '&eProtection &f' . HCFUtils::PAID_PROTECTION . '&7, &eSharpness &f' . HCFUtils::PAID_SHARPNESS . TextFormat::EOL .
            '&ePlayers per faction &f' . FactionUtils::MAXIMUM_MEMBERS . '&7, &eAllies per faction &f' . FactionUtils::MAXIMUM_ALLIES . TextFormat::EOL .
            '&7&m--------------------------------';
    }
}