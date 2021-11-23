<?php

namespace ImAMadDev\player;

use ImAMadDev\HCF;
use pocketmine\utils\SingletonTrait;

define("PLAYER_DIRECTORY", HCF::getInstance()->getDataFolder() . "players");

class PlayerCache
{
    use SingletonTrait;

    public function __construct(
        private string $name,
        private array $data
    ){}

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
    public function getJsonData() : string
    {
        return json_encode($this->getData());
    }

    public function setInData(string $key, mixed $value) : void
    {
        $this->data[$key] = $value;
    }

    public function getInData(string $key) : mixed
    {
        return $this->data[$key];
    }

    public function __destruct()
    {
        if (!file_exists(PLAYER_DIRECTORY . $this->name . ".js")) return;
        file_put_contents(PLAYER_DIRECTORY . $this->name . ".js", $this->getJsonData());
    }

}