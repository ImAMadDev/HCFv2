<?php /** @noinspection ALL */

namespace ImAMadDev\entity;

use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
use pocketmine\entity\Entity;
use pocketmine\utils\TextFormat;
use pocketmine\entity\Villager;
use pocketmine\nbt\tag\{CompoundTag, ListTag, DoubleTag, StringTag};
use pocketmine\nbt\NBT;
use pocketmine\item\Item;
use pocketmine\world\particle\HugeExplodeSeedParticle;

use ImAMadDev\player\HCFPlayer;
use ImAMadDev\faction\{FactionUtils, Faction};
use ImAMadDev\HCF;
use ImAMadDev\utils\HCFUtils;
use pocketmine\world\sound\ExplodeSound;

class CombatLogger extends Villager{

	private int $time = 500;
	
	public ?HCFPlayer $lastDamager = null;
	
	public bool $despawned = false;
    private string $faction;
    private string $player;

    public function initEntity(CompoundTag $nbt): void {
        parent::initEntity($nbt);
        $this->setMaxHealth(100);
        $this->setHealth(100);
        $this->time = 550;
        $player = $nbt->getString("player", "");
        $this->setPlayer($player);
        $faction = $nbt->getString("faction", "");
        $this->setFaction($faction);
    }
	
	public function load(int $time = 500) : void {
		$this->time = $time;
		$this->setHealth(100);
		$this->setMaxHealth(100);
	}

    public function setPlayer(string $name) : void
    {
        $this->player = $name;
        $this->networkPropertiesDirty = true;
    }

    public function setFaction(string $name) : void
    {
        $this->faction = $name;
        $this->networkPropertiesDirty = true;
    }

    public function saveNBT() : CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setString("player", $this->getPlayerName());
        $nbt->setString("faction", $this->getFactionName());
        return $nbt;
    }
	
	public function getFactionName() : ? string {
		return $this->faction;
	}
	
	public function getPlayerName() : ? string {
		return $this->player;
	}

    protected function syncNetworkData(EntityMetadataCollection $properties) : void{
        parent::syncNetworkData($properties);

        $properties->setString(160, $this->player);
        $properties->setString(170, $this->faction);
    }
	
	public function getName(): string{
		return "CombatLogger";
	}
	
	public function isOnlinePlayer() : bool {
		if(Server::getInstance()->getPlayerByPrefix($this->getPlayerName()) instanceof HCFPlayer) return true;
		return false;
	}
	
	public function getFaction() : ? Faction {
		return HCF::getInstance()->getFactionManager()->getFaction($this->getFactionName());
	}
	
	public function entityBaseTick(int $tickDiff = 1): bool{
		$this->setNameTag(TextFormat::GRAY . "(Combat-Logger) " . TextFormat::DARK_BLUE . $this->getPlayerName() . TextFormat::EOL . TextFormat::RED . "Despawn in: " . gmdate("i:s", $this->time));
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
		
		if ($this->getPlayerName() === "" || $this->isOnlinePlayer() === true) {
			$this->despawned = true;
			$this->flagForDespawn();
			return false;
		}
		
		if ($this->time <= 0) {
			$this->despawned = true;
			$this->kill();
		} else {
			$this->time--;
		}
		return parent::entityBaseTick($tickDiff);
	}
	
	protected function onDeath() : void{
		parent::onDeath();
		if($this->despawned === true) return;
		if(($faction = $this->getFaction()) instanceof Faction) {
			$faction->removeDTR(FactionUtils::LOSE_DTR);
		}
		$drops = [];
        $namedTag = Server::getInstance()->getOfflinePlayerData($this->getPlayerName());
        if ($namedTag instanceof CompoundTag) {
            $content = $namedTag->getListTag("Inventory");
            if ($content !== null) {
                /** @var CompoundTag $item */
                foreach ($content as $i => $item) {
                    $drops[] = Item::nbtDeserialize($item);
                }
            }
        }
		$namedTag->setTag("Inventory", new ListTag([], NBT::TAG_Compound));
        $namedTag->setTag("Pos", new ListTag([
            new DoubleTag(0),
            new DoubleTag(100),
            new DoubleTag(0)
        ]));
		$namedTag->setTag("Level", new StringTag(HCFUtils::DEFAULT_MAP));
		Server::getInstance()->saveOfflinePlayerData($this->getPlayerName(), $namedTag);
		foreach($drops as $item) {
			$this->getWorld()->dropItem($this->getPosition(), $item);
		}
		if($this->lastDamager instanceof HCFPlayer) {
			$this->lastDamager->obtainKill($this->getPlayerName());
			Server::getInstance()->broadcastMessage(TextFormat::colorize("&c " . $this->getPlayerName() . " &7(Combat-Logger) &ewas slain by &c" . $this->lastDamager->getName() . "&7[&4" . $this->lastDamager->getCache()->getInData('kills', true, 0) . "&7]&e using " . $this->lastDamager->getHandName()));
		} else {
			Server::getInstance()->broadcastMessage(TextFormat::colorize("&c " . $this->getPlayerName() . " &7(Combat-Logger) &esome how die!"));
		}
		$this->getWorld()->addParticle($this->getPosition(), new HugeExplodeSeedParticle());
		$this->getWorld()->addSound($this->getPosition(), new ExplodeSound());
		$this->flagForDespawn();
	}
	
	public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4): void{}
}