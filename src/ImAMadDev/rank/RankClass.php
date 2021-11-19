<?php

namespace ImAMadDev\rank;

use pocketmine\utils\{TextFormat, Config};

use ImAMadDev\HCF;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\rank\ticks\UpdateDataAsyncTask;

class RankClass {

    /**
     * @var HCF|null
     */
	public ?HCF $main = null;

    /**
     * @var array|null
     */
	public ?array $data = null;

    /**
     * @var Config|null
     */
	public ?Config $config = null;

    /**
     * @param HCF $main
     * @param array $data
     */
	public function __construct(HCF $main, array $data) {
		$this->main = $main;
		$this->data = $data;
		$this->config = new Config($main->getDataFolder() . "ranks/" . $data['name'] . ".yml");
		$this->main->getLogger()->info(TextFormat::GREEN."Rank » {$data['name']} was loaded successfully!");
	}

    /**
     * @return string
     */
	public function getName() : string {
		return $this->data['name'];
	}
	
	public function getFormat() : string {
		return $this->data['format'];
	}
	
	public function getTag() : string {
		return $this->data['tag'];
	}
	
	public function getFormatForPlayer(? Faction $faction = null, string $name, string $message = "") : string {
		$factionName = $faction === null ? "" : $faction->getName();
		$replaced = str_replace(["{faction}", "{name}", "{message}"], [$factionName, $name, $message], $this->data['format']);
		return TextFormat::colorize($replaced);
	}
	
	public function getTagForPlayer(? Faction $faction = null, string $name) : string {
		$factionName = $faction === null ? "" : $faction->getName();
		$replaced = str_replace(["{faction}", "{name}"], [$factionName, $name], $this->data['tag']);
		return TextFormat::colorize($replaced);
	}
	
	public function getPermissions() : array {
		return $this->data['permissions'];
	}
	
	public function hasPermission(string $permission) : bool {
		return in_array($permission, $this->getPermissions());
	}
	
	public function addPermission(string $permission) : void {
		$new = [];
		$new[] = $permission;
		foreach($this->getPermissions() as $perms) {
			if($perms === $permission or in_array($perms, $new)) {
				continue;
			}
			$new[] = $perms;
		}
		$this->data['permissions'] = $new;
		$this->updateData('permissions', $this->data['permissions']);
	}
	
	public function setFormat(string $format) : void {
		$this->data['format'] = $format;
		$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask("format", $this->getFormat(), $this->getName(), false));
	}
	
	public function setTag(string $tag) : void {
		$this->data['tag'] = $tag;
		$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask("tag", $this->getTag(), $this->getName(), false));
	}
	
	public function updateData($key, $value, $nested = false): void {
		if($nested) {
			$this->config->setNested($key, $value);
        } else {
			$this->config->set($key, $value);
        }
        $this->config->save();
    }
	
	public function giveTo(HCFPlayer $player) : void {
		$this->givePermissions($player);
	}
	
	public function givePermissions(HCFPlayer $player): void {
		$given = [];
		foreach ($this->getPermissions() as $permission) {
			if (!in_array($permission, $given)) {
				$player->addAttachment($this->main)->setPermission($permission, true);
				$given[] = $permission;
			}
		}
		unset($given);
		//$player->recalculatePermissions();
	}

}