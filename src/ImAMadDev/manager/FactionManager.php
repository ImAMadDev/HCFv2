<?php

namespace ImAMadDev\manager;

use pocketmine\utils\{Config, Filesystem, SingletonTrait, TextFormat};

use ImAMadDev\claim\utils\ClaimFlags;
use ImAMadDev\claim\utils\ClaimType;
use ImAMadDev\claim\utils\EditClaimFlag;
use ImAMadDev\HCF;
use ImAMadDev\claim\Claim;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;
use JsonException;

class FactionManager {
    use SingletonTrait;
	
	public static ?HCF $main = null;
	public static array $factions = [];
	
	public function __construct(HCF $main) {
		self::$main = $main;
        self::setInstance($this);
		$this->init();
	}
	
	public function getFactions() : array {
		return self::$factions;
	}
	
	public function getFaction(?string $name) : ? Faction {
		if($name === null) return null;
		return self::$factions[$name] ?? null;
	}

    public function getFactionByPlayer(HCFPlayer | string $player) :?Faction
    {
        if ($player instanceof HCFPlayer) $player = $player->getName();
        foreach (self::$factions as $faction) if ($faction->isInFaction($player)) return $faction;
        return null;
    }

	public function isFaction($faction) : bool {
		if($faction instanceof Faction)  $faction = $faction->getName();
		return in_array($faction, array_keys(self::$factions));
	}
	
	public function equalFaction($faction1, $faction2) : bool {
		if($faction1 instanceof Faction) $faction1 = $faction1->getName();
		if($faction2 instanceof Faction) $faction2 = $faction2->getName();
		if($faction1 === null or $faction2 === null) return false;
		return ($faction1 === $faction2);
	}
	
	private function init() : void {
		if(!is_dir(self::$main->getDataFolder() . "factions/")) {
			@mkdir(self::$main->getDataFolder() . "factions/");
		}
		foreach(glob(self::$main->getDataFolder() . "factions/" . "*.yml") as $file) {
            $content = yaml_parse(Config::fixYAMLIndexes(file_get_contents($file)));
			self::$factions[basename($file, ".yml")] = new Faction(self::$main, $content);
			$data = ["name" => basename($file, ".yml"), "x1" => $content['x1'], "z1" => $content["z1"], "x2" => $content["x2"], "z2" => $content["z2"], "level" => $content["level"], 'claim_type' => ClaimType::FACTION];
			$claim = new Claim(HCF::getInstance(), $data);
			if($data['x1'] && $data['x2'] !== null) {
                $claim->getProperties()->addFlag(ClaimFlags::INTERACT_CANCEL, new EditClaimFlag([330, 324, 71, 64, 93, 94, 95, 96, 97, 107, 183, 184, 185, 186, 187, 167], true));
                $claim->getProperties()->addFlag(ClaimFlags::INTERACT, new EditClaimFlag([3, 58, 61, 62, 54, 205, 218, 145, 146, 116, 130, 154]));
                $claim->getProperties()->addFlag(ClaimFlags::BREAK, new EditClaimFlag());
                $claim->getProperties()->addFlag(ClaimFlags::PLACE, new EditClaimFlag());
			    ClaimManager::getInstance()->addClaim($claim);
			    self::$main->getLogger()->info(TextFormat::GREEN."Claim » {$claim->getProperties()->getName()} was loaded successfully!");
			}
		}
	}

    /**
     * @param array $data
     * @param HCFPlayer $player
     * @return bool
     */
    public function createFaction(array $data, HCFPlayer $player) : bool {
		if($this->validate($data)){
            $data['claim_type'] = ClaimType::FACTION;
            $content = yaml_emit($data, YAML_UTF8_ENCODING);
			self::$factions[$data['name']] = new Faction(self::$main, $data);
            Filesystem::safeFilePutContents(self::$main->getDataFolder() . "factions/" . $data['name'] . ".yml", $content);
            $player->getCache()->setInData('faction', $data['name']);
			$player->setFaction(self::$factions[$data['name']]);
			return true;
		} else {
			return false;
		}
	}
	
	public function validate(array $contents): bool{
		if(empty($contents)) return false;
		$needs = ["name", "leader", "coleaders", "members", "allys", "points", "kills", "dtr", "level", "home", "x1", "z1", "x2", "z2"];
		$errors = 0;
		foreach($needs as $need){
			if(!in_array($need, array_keys($contents))){
				$errors++;
			}
		}
		return $errors === 0;
	}
	
	public function disband(string $name) : void {
		if(is_file(self::$main->getDataFolder() . "factions/" . $name . ".yml")) @unlink(self::$main->getDataFolder() . "factions/" . $name . ".yml");
		unset(self::$factions[$name]);
	}
	
	public function getTopPower(): string{
		$points = [];
		foreach(self::$factions as $faction) {
			if($faction->isDisqualified()) continue;
			$points[$faction->getName()] = $faction->getPoints();
		}
		arsort($points);
		$top = 0;
		$message = TextFormat::DARK_GREEN . TextFormat::BOLD . "FACTIONS LEADERBOARD " . TextFormat::RESET . TextFormat::EOL;
		foreach($points as $name => $count){
			if($count <= 0 || $top === 10) break;
			$top++;
			$message .= TextFormat::BOLD . TextFormat::AQUA . $top . TextFormat::RESET . TextFormat::DARK_GREEN . $name . TextFormat::DARK_GRAY . " | " . TextFormat::GREEN . "$" . $count;
		}
		return $message;
	}

    public function validateAll() : void
    {
        foreach (self::$factions as $faction) {
            if (HCF::getInstance()->getCache($faction->getLeader())?->getInData('faction') !== $faction->getName()){
                $faction->disband();
            }
        }
    }
	
}