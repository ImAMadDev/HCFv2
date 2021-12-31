<?php

namespace ImAMadDev\player;

use ImAMadDev\faction\Faction;
use ImAMadDev\faction\FactionUtils;
use ImAMadDev\HCF;
use ImAMadDev\manager\FactionManager;
use ImAMadDev\player\modules\FactionRank;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

define("PLAYER_DIRECTORY", HCF::getInstance()->getDataFolder() . "players" . DIRECTORY_SEPARATOR);
define("COUNTDOWN", '_countdown');
class PlayerCache
{
    use SingletonTrait;
    
    private FactionRank $factionRank;

    #[Pure] public function __construct(
        private string $name,
        private array $data
    ){
        $this->factionRank = new FactionRank($this->name);
       // $this->loadFactionRank();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getJsonData() : string
    {
        $jsonOptions = JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING;
        return json_encode($this->getData(), $jsonOptions);
    }

    /**
     * @return Player|null
     */
    public function getPlayer() : ?Player
    {
        return Server::getInstance()->getPlayerByPrefix($this->getName());
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param bool $lowerCase
     */
    public function setInData(string $key, mixed $value, bool $lowerCase = false) : void
    {
        if ($lowerCase) $key = strtolower($key);
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param bool $lowerCase
     * @param mixed|null $default
     * @return mixed
     */
    public function getInData(string $key, bool $lowerCase = false, mixed $default = null) : mixed
    {
        if ($lowerCase) $key = strtolower($key);
        return $this->data[$key] ?? $default;
    }

    public function getCountdown(string $countdown) : int
    {
        return $this->getInData($countdown . COUNTDOWN, true) ?? 0;
    }

    public function hasCountdown(string $countdown, int $time = 60) : bool
    {
        if ($this->getCountdown($countdown) == 0) return false;
        return ($time - (time() - $this->getCountdown($countdown))) > 0;
    }

    public function setCountdown(string $countdown, int $time) : void
    {
        $this->setInData($countdown . COUNTDOWN, (time() + $time), true);
    }

    /**
     * @param string $data
     * @param string $arrayName
     * @return bool
     */
    public function hasDataInArray(string $data, string $arrayName = 'ranks') : bool
    {
        return in_array($data, $this->getInData($arrayName) ?? [], true);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addInArray(string $key, string $value) : void
    {
        $current = $this->getInData($key);
        $current[] = $value;
        $this->setInData($key, $current);
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function removeInArray(string $key, string $value) : void
    {
        if ($key === 'ranks' and $value === 'User') return;
        $current = [];
        foreach ($this->getInData($key) as $inDatum) {
            if ($inDatum == $value) continue;
            $current[] = $inDatum;
        }
        $this->setInData($key, $current);
    }

    public function removeInData(string $key, bool $lowerCase = false) : void
    {
        if ($lowerCase) $key = strtolower($key);
        unset($this->data[$key]);
    }

    public function saveData() : void
    {
        if (!file_exists(PLAYER_DIRECTORY . $this->name . ".js")) return;
        file_put_contents(PLAYER_DIRECTORY . $this->name . ".js", $this->getJsonData());
    }

    public function __destruct()
    {
        if (file_exists(PLAYER_DIRECTORY . $this->name . ".js")) {
            file_put_contents(PLAYER_DIRECTORY . $this->name . ".js", $this->getJsonData());
        }
    }

    public function loadFactionRank() : void
    {
        $faction = FactionManager::getInstance()->getFactionByPlayer($this->getName());
        if ($this->getInData('faction') === null){
            if ($faction instanceof Faction){
                if ($faction->isLeader($this->getName())) {
                    $faction->disband();
                    return;
                }
                if ($faction->isInFaction($this->getName())) {
                    $faction->removeMember($this->getName());
                    var_dump("jjjjj");
                    return;
                }
                $this->getFactionRank()->set(null);
            }
        } else {
            if ($faction instanceof Faction){
                if ($faction->isLeader($this->getName())) $this->getFactionRank()->set(FactionUtils::LEADER);
                if ($faction->isCoLeader($this->getName())) $this->getFactionRank()->set(FactionUtils::CO_LEADER);
                if ($faction->isMember($this->getName())) $this->getFactionRank()->set(FactionUtils::MEMBER);
            } else {
                $this->setInData('faction', null);
            }
        }
    }

    /**
     * @return FactionRank
     */
    public function getFactionRank(): FactionRank
    {
        return $this->factionRank;
    }

}