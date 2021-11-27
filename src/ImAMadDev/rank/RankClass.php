<?php

namespace ImAMadDev\rank;

use ImAMadDev\utils\InventoryUtils;
use pocketmine\form\FormValidationException;
use pocketmine\plugin\PluginException;
use pocketmine\utils\{TextFormat, Config};

use ImAMadDev\HCF;
use ImAMadDev\faction\Faction;
use ImAMadDev\player\HCFPlayer;
use ImAMadDev\rank\ticks\UpdateDataAsyncTask;

define("RANKS_DIRECTORY", HCF::getInstance()->getDataFolder() . 'ranks' . DIRECTORY_SEPARATOR);
class RankClass {


    /**
     * @param HCF $main
     * @param array $data
     */
	public function __construct(
        public HCF $main,
        public array $data) {
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
	
	public function getFormatForPlayer(?Faction $faction, string $name, string $message = "") : string {
		$factionName = $faction === null ? "" : $faction->getName();
		$replaced = str_replace(["{faction}", "{name}", "{message}"], [$factionName, $name, $message], $this->data['format']);
		return TextFormat::colorize($replaced);
	}
	
	public function getTagForPlayer(?Faction $faction, string $name) : string {
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
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function setFormat(string $format) : void {
		$this->data['format'] = $format;
		$this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}
	
	public function setTag(string $tag) : void {
		$this->data['tag'] = $tag;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
	}

    public function setReclaim(string $reclaim) : void
    {
        $this->data['reclaim'] = $reclaim;
        $this->main->getServer()->getAsyncPool()->submitTask(new UpdateDataAsyncTask($this->getName()));
    }

    public function getReclaim() : array
    {
        $reclaim = $this->data['reclaim'] ?? "";
        return InventoryUtils::decodeItems($reclaim);
    }

	public function updateData(): void {
        if (!file_exists(RANKS_DIRECTORY . $this->getName()  . '.yml')) return;
        file_put_contents(RANKS_DIRECTORY . $this->getName() . '.yml', yaml_emit($this->data, YAML_UTF8_ENCODING));
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

    public function __destruct()
    {
        if (!file_exists(RANKS_DIRECTORY . $this->getName()  . '.yml')) return;
        file_put_contents(RANKS_DIRECTORY . $this->getName() . '.yml', yaml_emit($this->data, YAML_UTF8_ENCODING));

    }

}