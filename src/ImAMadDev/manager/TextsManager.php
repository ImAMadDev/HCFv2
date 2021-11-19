<?php

namespace ImAMadDev\manager;

use pocketmine\utils\{Config, SingletonTrait, TextFormat};
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\Position;

use ImAMadDev\HCF;
use ImAMadDev\texts\Texts;
use ImAMadDev\texts\ticks\TextsTick;

use floatingtext\FloatingTextApi;

class TextsManager {
    use SingletonTrait;
	
	public static ?HCF $main = null;
	public static array $texts = [];
	
	private static Task |null $task = null;
	
	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$this->init();
	}
	public function init() : void {
		if(self::$task !== null) {
			self::$task->getHandler()->cancel();
			self::$task = null;
		}
		foreach(self::$main->getServer()->getOnlinePlayers() as $player) {
			foreach(self::$texts as $text) {
				FloatingTextApi::removeText($text->textID, $player);
			}
		}
		self::$texts = [];
		$config = new Config(self::$main->getDataFolder() . "floatingTexts.yml", Config::YAML, ["texts" => []]);
		if(count($config->get("texts")) > 0 ) {
			foreach($config->get("texts") as $text => $id) {
				self::$texts[$text] = new Texts(self::$main, ["name" => $text, "id" => FloatingTextApi::createText(new Vector3($id['x'], $id['y'], $id['z'])), "text" => str_replace("{line}", "\n", $id["text"]), "x" => $id['x'], "y" => $id['y'], "z" => $id['z'], "level" => $id['level']]);
			}
			self::$main->getScheduler()->scheduleRepeatingTask(self::$task = new TextsTick($this), 40);
		}
	}
	
	public function existText(string $text) : bool {
		$config = new Config(self::$main->getDataFolder() . "floatingTexts.yml", Config::YAML, ["texts" => []]);
		if(in_array($text, array_keys($config->get("texts", [])))) return true;
		return false;
	}
	
	public function addText(string $name, string $text, Position $pos) : void {
		$config = new Config(self::$main->getDataFolder() . "floatingTexts.yml", Config::YAML, ["texts" => []]);
		$config->setNested("texts.".$name.".x", round($pos->getX()));
		$config->setNested("texts.".$name.".y", round($pos->getY()));
		$config->setNested("texts.".$name.".z", round($pos->getZ()));
		$config->setNested("texts.".$name.".level", $pos->getWorld()->getFolderName());
		$config->setNested("texts.".$name.".text", $text);
		$config->save();
		$this->init();
	}
	
	public function editText(string $name, string $text) : void {
		$config = new Config(self::$main->getDataFolder() . "floatingTexts.yml", Config::YAML, ["texts" => []]);
		$config->setNested("texts.".$name.".text", $text);
		$config->save();
		$this->init();
	}
	
	public function removeText(string $remove): void {
		$id = self::$texts[$remove]->textID;
		unset(self::$texts[$remove]);
		foreach(self::$main->getServer()->getOnlinePlayers() as $player) {
			FloatingTextApi::removeText($id, $player);
		}
		$config = new Config(self::$main->getDataFolder() . "floatingTexts.yml", Config::YAML, ["texts" => []]);
		$config->removeNested("texts.".$remove);
		$config->save();
		$this->init();
	}
	
	public function getTexts() : array {
		return self::$texts;
	}
	
	public function getText(string $name) : ?Texts {
		return self::$texts[$name] ?? null;
	}
	
	public function getTextList() : string {
		$message = TextFormat::GREEN . "Texts List\n";
		foreach(self::$texts as $text) {
			$message .= TextFormat::GOLD . "name: " . $text->getName() . " Text: " . $text->getText() . "\n";
		}
		return $message;
	}
	
	public function isText(string $name) : bool {
		return in_array($name, array_keys(self::$texts));
	}
	
}