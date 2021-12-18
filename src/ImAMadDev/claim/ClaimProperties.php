<?php

namespace ImAMadDev\claim;

use ImAMadDev\claim\utils\ClaimType;
use JetBrains\PhpStorm\Pure;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;

class ClaimProperties
{

    private Position $position_1, $position_2;

    private array $flags = [];

    public function __construct(
      private array $data
    ){
        $world = Server::getInstance()->getWorldManager()->getWorldByName($this->data['level']);
        $this->setPosition1(new Position((float)$this->data['x1'], World::Y_MIN, (float)$this->data['z1'], $world));
        $this->setPosition2(new Position((float)$this->data['x2'], World::Y_MAX, (float)$this->data['z2'], $world));
    }

    public function getName() : string
    {
        return $this->data['name'];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function getYamlData(): bool|string
    {
        return yaml_emit($this->getData(), YAML_UTF8_ENCODING);
    }

    /**
     * @return Position
     */
    public function getPosition1(): Position
    {
        return $this->position_1;
    }

    /**
     * @param Position $position_1
     */
    public function setPosition1(Position $position_1): void
    {
        $this->position_1 = $position_1;
    }

    /**
     * @return Position
     */
    public function getPosition2(): Position
    {
        return $this->position_2;
    }

    /**
     * @param Position $position_2
     */
    public function setPosition2(Position $position_2): void
    {
        $this->position_2 = $position_2;
    }

    public function getWorld() : World
    {
        return Server::getInstance()->getWorldManager()->getWorldByName($this->data['level']);
    }

    #[Pure] public function isEditable() : bool
    {
        if ($this->getData()['claim_type'] === ClaimType::SPAWN) return false;
        if ($this->getData()['claim_type'] == ClaimType::KOTH) return false;
        if ($this->getData()['claim_type'] == ClaimType::WARZONE) return false;
        if ($this->getData()['claim_type'] == ClaimType::ROAD) return false;
        return true;
    }

    /**
     * @return array
     */
    public function getFlags() : array
    {
        return $this->flags;
    }

    /**
     * @param int $flag
     * @return bool
     */
    public function hasFlag(int $flag) : bool
    {
        return array_key_exists($flag, $this->flags);
    }

    /**
     * @param int $flag_type
     * @param mixed $values
     * @return void
     */
    public function addFlag(int $flag_type, mixed $values) : void
    {
        $this->flags[$flag_type] = $values;
    }

    /**
     * @param int $flag_type
     * @return mixed
     */
    public function getFlag(int $flag_type) : mixed
    {
        return $this->flags[$flag_type] ?? null;
    }

}