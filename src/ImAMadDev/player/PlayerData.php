<?php /** @noinspection ALL */

namespace ImAMadDev\player;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;

use ImAMadDev\HCF;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\rank\RankClass;
use ImAMadDev\faction\Faction;

class PlayerData {
	
	public static function register(string $playerName) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")){
			$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
			$config->setAll(["kills" => 0,
                "invincibility_time" => 3600,
                "lives" => 5,
                "lives_claimed" => false,
                "reclaim" => null,
                "ranks" => ["User"],
                "balance" => 1000,
                "faction" => null,
                "tags" => [],
                "currentTag" => null
            ]);
			$config->save();
			$ranks = $config->get("ranks", ["User"]);
			$factionName = $config->get("faction", null);
            $tag = $config->get("currentTag", null);
            HCF::getInstance()->createCache($playerName, $config->getAll());
		} else {
			$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
			$ranks = $config->get("ranks", ["User"]);
			$factionName = $config->get("faction", null);
            $tag = $config->get("currentTag", null);
		}
		if(($player = Server::getInstance()->getPlayerExact($playerName)) instanceof HCFPlayer) {
			foreach($ranks as $rankName) {
				$rank = HCF::getInstance()->getRankManager()->getRank($rankName);
				if($rank instanceof RankClass) {
					$rank->giveTo($player);
					$player->setRank($rank);
				}
			}
			$faction = HCF::getInstance()->getFactionManager()->getFaction($factionName);
			if($faction instanceof Faction) {
				$player->setFaction($faction);
			}
            $player->setCurrentTag($tag);
		}
	}
	
	
	public static function remove(Player $player) : void {
		if(file_exists(HCF::getInstance()->getDataFolder()."players/{$player->getName()}.js")){
			@unlink(HCF::getInstance()->getDataFolder()."players/{$player->getName()}.js");
		}
	}
	
	public static function removeData(string $playerName, string $data) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
		$config->remove($data);
		$config->save();
	}
	
	public static function hasData(string $playerName, string $data) : bool {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return false;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
		if($config->exists(strtolower($data))){
			return true;
		}else{
			return false;
		}
	}
	
	public static function getData(string $playerName) : ? Config {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return null;
		return new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
	}
	
	public static function setData(string $playerName, string $data, $value) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
		$config->set(strtolower($data), $value);
		$config->save();
	}
	
	public static function addRank(string $playerName, string $rank) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
		$new = [];
		$new[] = $rank;
		foreach($config->get("ranks", ["User"]) as $ranks) {
			if($ranks === $rank or in_array($ranks, $new)) {
				continue;
			}
			$new[] = $ranks;
		}
		$config->set("ranks", $new);
		$config->save();
	}
	
	public static function hasRank(string $playerName, string $rank) : bool {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return false;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
		foreach($config->get("ranks", ["User"]) as $ranks) {
			if($ranks === $rank) {
				return true;
			}
		}
		return false;
	}
	
	public static function removeRank(string $playerName, string $rank) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
		$new = [];
		foreach($config->get("ranks", ["User"]) as $ranks) {
			if($ranks === $rank && $rank !== "User") {
				continue;
			}
			$new[] = $ranks;
		}
		$config->set("ranks", $new);
		$config->save();
	}

    public static function addTag(string $playerName, string $tag) : void {
        if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
        $config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
        $new = [];
        $new[] = $tag;
        foreach($config->get("tags", []) as $tags) {
            if($tags === $tag or in_array($tags, $new)) {
                continue;
            }
            $new[] = $tags;
        }
        $config->set("tags", $new);
        $config->save();
    }

    public static function hasTag(string $playerName, string $tag) : bool {
        if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return false;
        $config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
        foreach($config->get("tags", []) as $tags) {
            if($tags === $tag) {
                return true;
            }
        }
        return false;
    }

    public static function removeTag(string $playerName, string $tag) : void {
        if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
        $config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
        $new = [];
        foreach($config->get("tags", []) as $tags) {
            if($tags === $tag) {
                continue;
            }
            $new[] = $tags;
        }
        $config->set("tags", $new);
        $config->save();
    }

    public static function selectTag(string $playerName, string|null $tag = null) : void
    {
        if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
        $config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON);
        $config->set("currentTag", $tag);
        $config->save();
    }
	
	public static function getKills(string $playerName) : int {
		return self::getData($playerName)->get("kills", 0) === null ? 0 : self::getData($playerName)->get("kills");
	}
	
	public static function getInvincibilityTime(string $playerName) : int {
		return self::getData($playerName)->get("invincibility_time", 3600) === null ? 3600 : self::getData($playerName)->get("invincibility_time");
	}
	
	public static function getLives(string $playerName) : int {
		return self::getData($playerName)->get("lives", 5) === null ? 5 : self::getData($playerName)->get("lives");
	}
	
	public static function getCountdown(string $playerName, string $cooldown) : int {
		return (self::hasData($playerName, $cooldown . '_countdown') == true) ? self::getData($playerName)->get(strtolower($cooldown . '_countdown'), 0) : 0;
	}

    public static function setCountdown(string $playerName, string $countdown, int $number) : void
    {
        self::setData($playerName, $countdown . '_countdown', $number);
    }
	
	public static function saveItem(string $playerName, string $item, int $count) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON); 
		$config->setNested("store." . $item, $count);
		$config->save();
	}
	
	public static function removeSavedItem(string $playerName, string $item) : void {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return;
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON); 
		$config->removeNested("store." . $item);
		$config->save();
	}
	
	public static function getSavedItems(string $playerName) : array {
		if(!file_exists(HCF::getInstance()->getDataFolder()."players/{$playerName}.js")) return [];
		$config = new Config(HCF::getInstance()->getDataFolder()."players/{$playerName}.js", Config::JSON); 
		return $config->get("store", []);
	}
}

?>